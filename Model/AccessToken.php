<?php

namespace RMS\PushNotificationsBundle\Model;

use Buzz\Message\Response;
use Gedmo\Timestampable\Traits\Timestampable;

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
    protected $tokenType;

    public function __construct($response = null)
    {
        if($response instanceof Response) {
            $content = json_decode($response->getContent());
            $this->token = $content->token;
            $this->expiresIn = $content->expiresIn;
            $this->scope = $content->scope;
            $this->tokenType = $content->tokenType;
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
        return $this;
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
     * Get tokenType
     *
     * @return string
     */
    public function getTokenType()
    {
        return $this->tokenType;
    }

    /**
     * Set tokenType
     *
     * @param $tokenType
     * @return $this
     */
    public function setTokenType($tokenType)
    {
        $this->tokenType = $tokenType;
        return $this;
    }

}