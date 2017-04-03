<?php

namespace RMS\PushNotificationsBundle\Message;

use RMS\PushNotificationsBundle\Device\Types;
use RMS\PushNotificationsBundle\Model\AccessToken;

class AmazonMessage extends AndroidMessage
{
    /**
     * @var AccessToken
     */
    protected $accessToken;

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
     * Gets the message body to send
     * This is primarily used in C2DM
     *
     * @return array
     */
    public function getMessageBody()
    {
        $data = array(
            'data' => array(
                'message' => $this->getMessage()
            )
        );

        if (!empty($this->data)) {
            $dataArray = array_merge($data['data'], $this->data);
            $data['data'] = $dataArray;
        }

        if($this->getCollapseKey() && is_string($this->getCollapseKey())) {
            $data['consolidationKey'] = $this->getCollapseKey();
        }

        if($this->getMd5() && is_string($this->getMd5())) {
            $data['md5'] = $this->getMd5();
        }

        if($this->getExpiresAfter() && is_integer($this->getExpiresAfter())) {
            $data['expiresAfter'] = $this->getExpiresAfter();
        }

        return $data;
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
