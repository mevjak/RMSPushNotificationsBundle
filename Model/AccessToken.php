<?php

namespace RMS\PushNotificationsBundle\Model;

use Buzz\Message\Response;
use Gedmo\Timestampable\Traits\Timestampable;
use Symfony\Component\Validator\Constraints\DateTime;

class AccessToken
{
    use Timestampable;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var integer
     */
    protected $expiresIn;

    /**
     * @var string
     */
    protected $scope;

    /**
     * @var string
     */
    protected $type;

    public function __construct($response = null)
    {
        if($response instanceof Response) {
            $content = json_decode($response->getContent());
            $this->token = $content->access_token;
            $this->setExpiresIn($content->expires_in);
            $this->scope = $content->scope;
            $this->type = $content->token_type;
        }
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set token
     *
     * @param $token
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * Get expiresIn
     *
     * @return int
     */
    public function getExpiresIn()
    {
        return $this->expiresIn;
    }

    /**
     * Set expiresIn
     *
     * @param $expiresIn
     * @return $this
     */
    public function setExpiresIn($expiresIn)
    {
        $this->expiresIn = $expiresIn;
        $dateTime = new \DateTime();
        $dateTime->modify(sprintf('+%s seconds', $this->expiresIn));
        $this->setUpdatedAt($dateTime);
        return $this;
    }

    /**
     * @return bool
     */
    public function isExpired() {
        if(!$this->getExpiresIn()) {
            return true;
        }

        $currentDateTime = new \DateTime();
        // secure dateTime if the processing time to send a new push messages takes to long
        $currentDateTime->modify('-5 minutes');
        if($currentDateTime >= $this->getUpdatedAt()) {
            return true;
        }

        return false;
    }

    /**
     * Get scope
     * 
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * Set scope
     * 
     * @param $scope
     * @return $this
     */
    public function setScope($scope)
    {
        $this->scope = $scope;
        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

}