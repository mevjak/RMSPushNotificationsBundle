<?php

namespace RMS\PushNotificationsBundle\Service\OS;

use Buzz\Client\Curl;
use Buzz\Message\Response;
use RMS\PushNotificationsBundle\Exception\InvalidMessageTypeException;
use RMS\PushNotificationsBundle\Message\AmazonMessage;
use RMS\PushNotificationsBundle\Message\MessageInterface;
use Buzz\Browser;
use RMS\PushNotificationsBundle\Model\AccessToken;

class AmazonNotification implements OSNotificationServiceInterface {

    /**
     * @var Browser
     */
    private $browser;

    /**
     * @var string
     */
    private $scheme;

    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $clientSecret;

    /**
     * @var AccessToken
     */
    private $accessToken;

    /**
     * @var string
     */
    private $accessTokenEndpoint;

    /**
     * @var string
     */
    private $messageEndpoint;

    const SCOPE = 'messaging:push';

    const GRANT_TYPE = 'client_credentials';

    public function __construct($scheme, $host, $clientId, $clientSecret, $accessTokenEndpoint, $messageEndpoint, $timeout, $logger) {
        $client = new Curl();
        $client->setTimeout($timeout);
        $this->browser = new Browser($client);
        $this->browser->getClient()->setVerifyPeer(false);
        $this->scheme = $scheme;
        $this->host = $host;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->accessTokenEndpoint = $accessTokenEndpoint;
        $this->messageEndpoint = $messageEndpoint;
        $this->logger = $logger;
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
            'scope' => self::SCOPE,
            'grant_type' => self::GRANT_TYPE
        );

        $uri = $this->getEndpointUri($this->accessTokenEndpoint);
        $this->browser->post($uri, $headers, json_encode($data));

        /** @var Response $response */
        $response = $this->browser->getLastResponse();
        return $response;
    }

    /**
     * @param MessageInterface $message
     * @param $accessToken
     * @param null $consolidationKey
     * @param int $expiresAfter
     * @param null $md5
     * @return Response
     */
    public function send(MessageInterface $message)
    {
        if (!$message instanceof AmazonMessage) {
            throw new InvalidMessageTypeException(sprintf("Message type '%s' not supported by Amazon", get_class($message)));
        }

        if(!$this->getAccessToken() || !$this->getAccessToken()->getExpiresIn()) {
            $accessTokenResponse = $this->sendAccessTokenRequest();
            $this->setAccessToken($accessTokenResponse);
        }
        else {
            $this->setAccessToken($message->getAccessToken());
        }

        $accessToken = $this->getAccessToken();

        $headers = array(
            'Content-Type' => 'application/json',
            'Authorization' => sprintf('bearer %s', urlencode($accessToken))
        );

        $data = $message->getMessageBody();
        $endpoint = $this->getEndpointUri($this->getMessageEndpoint($message->getDeviceIdentifier()));
        $uri = $this->getEndpointUri($endpoint);
        $this->browser->post($uri, $headers, json_encode($data));

        /** @var Response $response */
        $response = $this->browser->getLastResponse();
        return $response;
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
}