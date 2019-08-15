<?php


namespace RMS\PushNotificationsBundle\Exception;


use RMS\PushNotificationsBundle\Model\Payload;

/**
 * Class InvalidPayloadException
 * @package RMS\PushNotificationsBundle
 */
class InvalidPayloadException extends \Exception
{
    public static function reservedKey()
    {
        return new static("Key " . Payload::PAYLOAD_ROOT_KEY . " is reserved and can't be used for custom property.");
    }

    public static function notExistingCustomValue($key)
    {
        return new static("Custom value with key '$key' doesn't exist.");
    }
}