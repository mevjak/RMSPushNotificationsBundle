<?php

namespace RMS\PushNotificationsBundle\Service\OS;

use Buzz\Client\Curl;
use Buzz\Client\MultiCurl;
use Buzz\Message\Response;
use opwoco\Core\LogBundle\Logging\CustomChannelLogger;
use RMS\PushNotificationsBundle\Exception\InvalidMessageTypeException;
use RMS\PushNotificationsBundle\Message\AmazonMessage;
use RMS\PushNotificationsBundle\Message\MessageInterface;
use Buzz\Browser;
use RMS\PushNotificationsBundle\Model\AccessToken;
use RMS\PushNotificationsBundle\Service\Notifications;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class AndroidADMNotification extends Notifications implements OSNotificationServiceInterface {

    /**
     * @var Browser
     */
    protected $browser;

    /**
     * @var string
     */
    protected $scheme = 'https';

    /**
     * @var string
     */
    protected $host = 'api.amazon.com';

    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $clientSecret;

    /**
     * @var bool
     */
    protected $useMultiCurl;

    /**
     * @var CustomChannelLogger
     */
    protected $logger;

    /**
     * @var AccessToken
     */
    protected $accessToken;

    /**
     * @var string
     */
    protected $accessTokenEndpoint = 'auth/o2/token';

    /**
     * @var string
     */
    protected $messageEndpoint = 'messaging/registrations/%s/messages';

    /**
     * @var string
     */
    protected $scope = 'messaging:push';

    /**
     * @var string
     */
    protected $grantType = 'client_credentials';

    /**
     * @var array
     */
    protected $responses = array();

    public function __construct($clientId, $clientSecret, $useMultiCurl, $timeout, $logger) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->useMultiCurl = $useMultiCurl;
        $this->logger = $logger;
        $client = new Curl();
        $client->setTimeout($timeout);
        $this->browser = new Browser($client);
        $this->browser->getClient()->setVerifyPeer(false);

    }

    /**
     * Get accessToken
     * 
     * @param $clientId
     * @param $clientSecret
     * @return Response
     */
    public function sendAccessTokenRequest() {
        $headers = array(
            'Content-Type' => 'application/json'
        );

        $data = array(
            'client_id' => $this->clientId,    // The client ID assigned to you by the provider
            'client_secret' => $this->clientSecret,   // The client password assigned to you by the provider
            'scope' => $this->scope,
            'grant_type' => $this->grantType
        );

        $uri = $this->getEndpointUri($this->accessTokenEndpoint);
        $this->browser->post($uri, $headers, json_encode($data));

        /** @var Response $response */
        $response = $this->browser->getLastResponse();
        return $response;
    }

    /**
     * @param MessageInterface $message
     * @return bool
     */
    public function send(MessageInterface $message)
    {
        if (!$message instanceof AmazonMessage) {
            throw new InvalidMessageTypeException(sprintf("Message type '%s' not supported by Amazon Device Messaging", get_class($message)));
        }

        if(!$this->getAccessToken() || $this->getAccessToken()->isExpired()) {
            $accessTokenResponse = $this->sendAccessTokenRequest();
            $this->setAccessToken($accessTokenResponse);
        }
        else {
            $this->setAccessToken($message->getAccessToken());
        }

        $accessToken = $this->getAccessToken();

        $headers = array(
            'Content-Type' => 'application/json',
            'accept' => 'application/json',
            'Authorization' => sprintf('%s %s', $accessToken->getType(), urlencode($accessToken->getToken()))
        );

        $data = $message->getMessageBody();


        // Perform the calls (in parallel)
        $this->responses = array();

        foreach($message->getIdentifiers() as $identifier){
            $uri = $this->getEndpointUri($this->getMessageEndpoint($identifier));
            $this->responses[] = $this->browser->post($uri, $headers, json_encode($data));
        }

        // If we're using multiple concurrent connections via MultiCurl
        // then we should flush all requests
        if ($this->browser->getClient() instanceof MultiCurl) {
            $this->browser->getClient()->flush();
        }


        // Determine success
        /** @var Response $response */
        foreach ($this->responses as $response) {
            if(SymfonyResponse::HTTP_OK != $response->getStatusCode()) {
                switch($response->getStatusCode()) {
                    case SymfonyResponse::HTTP_BAD_REQUEST:
                    case SymfonyResponse::HTTP_UNAUTHORIZED:
                    case SymfonyResponse::HTTP_REQUEST_ENTITY_TOO_LARGE:
                    case SymfonyResponse::HTTP_TOO_MANY_REQUESTS:
                        $message = json_decode($response->getContent(), true);
                        $this->logger->error($message['reason'], $message);
                        break;
                    case SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR:
                    case SymfonyResponse::HTTP_SERVICE_UNAVAILABLE:
                        $this->logger->error($response->getStatusCode());
                        break;

                }
            }
        }

        return true;
    }

    /**
     * Get accessToken
     *
     * @return AccessToken
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Set accessToken
     * 
     * @param AccessToken|Response $accessToken
     * @return $this
     */
    public function setAccessToken($accessToken)
    {
        if($accessToken instanceof Response) {
            $this->accessToken = new AccessToken($accessToken);
        }
        else {
            $this->accessToken = $accessToken;
        }

        return $this;
    }

    /**
     * @param $registrationId
     * @return string
     */
    public function getMessageEndpoint($registrationId) {
        return sprintf($this->messageEndpoint, $registrationId);
    }

    /**
     * Get endpointUri
     *
     * @param $endpoint
     * @return string
     */
    public function getEndpointUri($endpoint) {
        $uri = sprintf('%s://%s/%s', $this->scheme, $this->host, $endpoint);
        return $uri;
    }

    /**
     * @return Browser
     */
    public function getBrowser()
    {
        return $this->browser;
    }
}