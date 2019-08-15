<?php

namespace RMS\PushNotificationsBundle\Interfaces;


/**
 * Interface ApnsResponseInterface
 * @package RMS\PushNotificationsBundle
 */
interface ApnsResponseInterface
{
    /**
     * Get APNs Id
     *
     * @return string
     */
    public function getApnsId();

    /**
     * Get status code.
     *
     * @return int|null
     */
    public function getStatusCode(): int;

    /**
     * Get reason phrase.
     *
     * @return string
     */
    public function getReasonPhrase(): string;

    /**
     * Get error reason.
     *
     * @return string
     */
    public function getErrorReason(): string;

    /**
     * Get error description.
     *
     * @return string
     */
    public function getErrorDescription(): string;

    /**
     * Get timestamp for a status 410 error
     *
     * @return string
     */
    public function get410Timestamp(): string;
}