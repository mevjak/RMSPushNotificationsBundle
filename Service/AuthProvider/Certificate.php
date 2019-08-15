<?php

namespace RMS\PushNotificationsBundle\Service\AuthProvider;

use RMS\PushNotificationsBundle\Interfaces\AuthProviderInterface;
use RMS\PushNotificationsBundle\Model\Request;

/**
 * Class Certificate
 * @package RMS\PushNotificationsBundle
 *
 * @see     http://bit.ly/communicating-with-apns
 */
class Certificate implements AuthProviderInterface
{
    /**
     * Path to certificate.
     *
     * @var string
     */
    private $certificatePath;

    /**
     * Certificate secret.
     *
     * @var string
     */
    private $certificateSecret;

    /**
     * The bundle ID for app obtained from Apple developer account.
     *
     * @var string
     */
    private $appBundleId;

    /**
     * This provider accepts the following options:
     *
     * - certificate_path
     * - certificate_secret
     *
     * @param array $options
     */
    private function __construct(array $options)
    {
        $this->certificatePath   = $options['certificate_path'] ;
        $this->certificateSecret = $options['certificate_secret'];
        $this->appBundleId       = $options['app_bundle_id'] ?? null;
    }

    /**
     * Create Certificate Auth provider.
     *
     * @param array $options
     * @return Certificate
     */
    public static function create(array $options): Certificate
    {
        return new self($options);
    }

    /**
     * Authenticate client.
     *
     * @param Request $request
     */
    public function authenticateClient(Request $request)
    {
        $request->addOptions(
            [
                CURLOPT_SSLCERT        => $this->certificatePath,
                CURLOPT_SSLCERTPASSWD  => $this->certificateSecret,
                CURLOPT_SSL_VERIFYPEER => true
            ]
        );
        $request->addHeaders([
            "apns-topic" => $this->appBundleId
        ]);
    }
}