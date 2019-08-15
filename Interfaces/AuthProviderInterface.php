<?php


namespace RMS\PushNotificationsBundle\Interfaces;


use RMS\PushNotificationsBundle\Model\Request;

/**
 * Interface AuthProviderInterface
 * @package RMS\PushNotificationsBundle
 */
interface AuthProviderInterface
{
    /**
     * Authenticate client
     *
     * @param Request $request
     * @return void
     */
    public function authenticateClient(Request $request);
}