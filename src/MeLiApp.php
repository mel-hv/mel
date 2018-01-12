<?php

namespace Mel;

class MeLiApp
{
    const ANONYMOUS_MODE = 'ANONYMOUS_CLIENT';

    /**
     * This is your client_id. It must be used to retrieve an access token
     * @var string
     */
    protected $appId;

    /**
     * This is used to retrieve an access token
     * @var string
     */
    protected $secretKey;

    /**
     * URL to return users to your app after they grant access
     * @var string
     */
    protected $redirectUri;

    /**
     * MeLiApp constructor.
     * @param string      $appId
     * @param string|null $secretKey
     * @param string|null $redirectUri
     * @throws \Exception
     */
    public function __construct($appId, $secretKey = null, $redirectUri = null)
    {
        if ($appId === self::ANONYMOUS_MODE) {
            $secretKey = self::ANONYMOUS_MODE;
            $redirectUri = self::ANONYMOUS_MODE;
        }

        if (empty($appId) || empty($secretKey) || is_null($appId) || is_null($secretKey)) {
            throw new \Exception(sprintf(
                'The %1$s:appId and %1$s:secretKey should not be empty or null',
                __CLASS__
            ));
        }

        if ($appId !== self::ANONYMOUS_MODE && !filter_var($redirectUri, FILTER_VALIDATE_URL)) {
            throw new \Exception(sprintf(
                'Use valid uri in %1$s:redirectUri',
                __CLASS__
            ));
        }

        $this->appId = $appId;
        $this->secretKey = $secretKey;
        $this->redirectUri = $redirectUri;
    }


    /**
     * Return configured client_id. It must be used to retrieve an access token
     * @return string
     */
    public function clientId()
    {
        return $this->appId;
    }

    /**
     * Return configured secret key
     * This is used to retrieve an access token
     * @return string
     */
    public function secretKey()
    {
        return $this->secretKey;
    }

    /**
     * Get the URL to return users to your app after they grant access
     * @return string
     */
    public function redirectUri()
    {
        return $this->redirectUri;
    }

    /**
     * Return if anonymous mode is activated
     * @return bool
     */
    public function isAnonymousClient()
    {
        return $this->clientId() === self::ANONYMOUS_MODE;
    }
}