<?php

namespace RMS\PushNotificationsBundle\Message;

use opwoco\Components\PushserverBundle\Factory\AndroidNotificationServiceFactory;
use RMS\PushNotificationsBundle\Device\Types;

class AndroidMessage implements MessageInterface
{
    const DEFAULT_COLLAPSE_KEY = 1;

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
    protected $collapseKey = self::DEFAULT_COLLAPSE_KEY;

    /**
     * A collection of device identifiers that the message
     * is intended for. GCM use only
     *
     * @var array
     */
    protected $allIdentifiers = array();

    /**
     * Options for messages
     *
     * @var array
     */
    protected $options = array();

    /**
     * @var string
     */
    protected $cloudMessagingService;

    /**
     * Sets the string message
     *
     * @param $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
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
        return $this;
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
            "registration_id" => $this->identifier,
            "collapse_key"    => $this->collapseKey,
            "data.message"    => $this->message,
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
     * Returns the target OS for this message
     *
     * @return string
     */
    public function getTargetOS()
    {
        switch($this->getCloudMessagingService()) {
            case AndroidNotificationServiceFactory::COULD_MESSAGING_SERIVCE_TYPE_ADM:
                $type = Types::OS_ANDROID_ADM;
                break;
            case AndroidNotificationServiceFactory::COULD_MESSAGING_SERIVCE_TYPE_FCM:
                $type = Types::OS_ANDROID_FCM;
                break;
            case AndroidNotificationServiceFactory::COULD_MESSAGING_SERIVCE_TYPE_GCM:
                $type = Types::OS_ANDROID_GCM;
                break;
            default:
                $type = Types::OS_ANDROID_C2DM;
                break;
        }

        return $type;
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
     * Android-specific
     * Returns the collapse key
     *
     * @return int
     */
    public function getCollapseKey()
    {
        return $this->collapseKey;
    }

    /**
     * Android-specific
     * Sets the collapse key
     *
     * @param $collapseKey
     */
    public function setCollapseKey($collapseKey)
    {
        $this->collapseKey = $collapseKey;
    }

    /**
     * Returns an array of device identifiers
     *
     * @return mixed
     */
    public function getIdentifiers()
    {
        return array_values($this->allIdentifiers);
    }

    /**
     * Adds a device identifier to the GCM list
     * @param string $identifier
     */
    public function addIdentifier($identifier)
    {
        $this->allIdentifiers[$identifier] = $identifier;
    }

    /**
     * Sets the GCM list
     * @param array $allIdentifiers
     */
    public function setAllIdentifiers($allIdentifiers) {
        $this->allIdentifiers = array_combine($allIdentifiers, $allIdentifiers);
    }

    /**
     * Set options
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * Returns options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Get cloudMessagingService
     *
     * @return string
     */
    public function getCloudMessagingService()
    {
        return $this->cloudMessagingService;
    }

    /**
     * Set cloudMessagingService
     *
     * @param $cloudMessagingService
     * @return $this
     */
    public function setCloudMessagingService($cloudMessagingService)
    {
        $this->cloudMessagingService = $cloudMessagingService;
        return $this;
    }
}
