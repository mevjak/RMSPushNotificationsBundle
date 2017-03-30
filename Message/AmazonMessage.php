<?php

namespace RMS\PushNotificationsBundle\Message;

use RMS\PushNotificationsBundle\Device\Types;
use RMS\PushNotificationsBundle\Model\AccessToken;

class AmazonMessage implements MessageInterface
{
    const DEFAULT_COLLAPSE_KEY = 1;

    /**
     * @var AccessToken
     */
    protected $accessToken;

    /**
     * String message
     *
     * @var string
     */
    protected $message = "";

    /**
     * The data to send in the message
     *
     * @var array
     */
    protected $data = array();

    /**
     * Identifier of the target device
     *
     * @var string
     */
    protected $identifier = "";

    /**
     * Collapse key for data
     *
     * @var int
     */
    protected $consolidationKey = self::DEFAULT_COLLAPSE_KEY;

    /**
     * @var integer
     */
    protected $expiresAfter;

    /**
     * @var string
     */
    protected $md5;

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
     * @param AccessToken $accessToken
     * @return $this
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    /**
     * Sets the string message
     *
     * @param $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * Returns the string message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Sets the data. For Android, this is any custom data to use
     *
     * @param array $data The custom data to send
     */
    public function setData($data)
    {
        $this->data = (is_array($data) ? $data : array($data));
    }

    /**
     * Returns any custom data
     *
     * @return array
     */
    public function getData()
    {
        return array_merge(array('message' => $this->getMessage()), $this->data);
    }

    /**
     * Gets the message body to send
     * This is primarily used in C2DM
     *
     * @return array
     */
    public function getMessageBody()
    {
        $data = array(
            'data' => $this->getMessage(),    // The client ID assigned to you by the provider
            'consolidationKey' => $this->getConsolidationKey(),   // The client password assigned to you by the provider
            'expiresAfter' => $this->getExpiresAfter(),
            'md5' => $this->getMd5()
        );

        if (!empty($this->data)) {
            $data = array_merge($data, $this->data);
        }

        return $data;
    }

    /**
     * Sets the identifier of the target device, eg UUID or similar
     *
     * @param $identifier
     */
    public function setDeviceIdentifier($identifier)
    {
        $this->identifier = $identifier;
        $this->allIdentifiers = array($identifier => $identifier);
    }


    /**
     * Returns the target device identifier
     *
     * @return string
     */
    public function getDeviceIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Get consolidationKey
     *
     * @return int
     */
    public function getConsolidationKey()
    {
        return $this->consolidationKey;
    }

    /**
     * Set consolidationKey
     *
     * @param $consolidationKey
     * @return $this
     */
    public function setConsolidationKey($consolidationKey)
    {
        $this->consolidationKey = $consolidationKey;
        return $this;
    }

    /**
     * Get expiresAfter
     *
     * @return int
     */
    public function getExpiresAfter()
    {
        return $this->expiresAfter;
    }

    /**
     * ExpiresAfter
     *
     * @param $expiresAfter
     * @return $this
     */
    public function setExpiresAfter($expiresAfter)
    {
        $this->expiresAfter = $expiresAfter;
        return $this;
    }

    /**
     * Get md5
     *
     * @return string
     */
    public function getMd5()
    {
        return $this->md5;
    }

    /**
     * Set md5
     *
     * @param $md5
     * @return $this
     */
    public function setMd5($md5)
    {
        $this->md5 = $md5;
        return $this;
    }


    public function getTargetOS()
    {
        return Types::OS_ANDROID_ADM;
    }
}
