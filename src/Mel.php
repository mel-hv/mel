<?php

namespace Mel;

use Mel\Auth\AccessToken;
use Mel\Auth\AccessTokenInterface;
use Mel\Auth\OAuthClient;
use Mel\Auth\Storage\SessionStorage;
use Mel\Exceptions\MelException;
use Mel\Http\ClientInterface;
use Mel\Http\HttpClient;

class Mel
{
    const VERSION = 0.1;

    /**
     * @var MeLiApp App configurations instance
     */
    protected $meLiApp;

    /**
     * @var Country Country object
     */
    protected $country;

    /**
     * @var ClientInterface Http client instance
     */
    protected $httpClient;

    /**
     * @var OAuthClient Client OAuth
     */
    protected $oAuthClient;

    /**
     * @var AccessToken Access token manager
     */
    protected $accessToken;

    /**
     * Mel constructor.
     *
     * @param MeLiApp              $meLiApp
     * @param Country|null         $country
     * @param AccessTokenInterface $accessToken
     *
     * @throws MelException
     * @throws \Exception
     */
    public function __construct(
        MeLiApp $meLiApp = null,
        Country $country = null,
        AccessTokenInterface $accessToken = null
    ) {
        $this->meLiApp = $meLiApp ?: new MeLiApp(MeLiApp::ANONYMOUS_MODE);
        $this->country = $country;

        if (!$this->meLiApp()->isAnonymousClient() && !($this->country instanceof Country)) {
            throw new MelException('Authenticated mode require a country');
        }

        $this->httpClient = $this->resolveHttpClient();

        if (!$this->meLiApp()->isAnonymousClient()) {
            $this->oAuthClient = new OAuthClient($this);
        }

        $this->accessToken = $accessToken ?: new AccessToken(new SessionStorage());
    }

    /**
     * Resolve HttpClient instance used for the Mel
     *
     * @return ClientInterface
     */
    protected function resolveHttpClient()
    {
        return new HttpClient($this);
    }

    /**
     * Return MeLiApp instance
     *
     * @return MeLiApp
     */
    public function meLiApp()
    {
        return $this->meLiApp;
    }

    /**
     * Return http client
     *
     * @return ClientInterface
     */
    public function httpClient()
    {
        return $this->httpClient;
    }


    /**
     * Return OAuth client instance
     *
     * @return OAuthClient
     */
    public function oAuthClient()
    {
        return $this->oAuthClient;
    }

    /**
     * Return country object
     *
     * @return Country|null
     */
    public function country()
    {
        return $this->country;
    }

    /**
     * Return access token object
     *
     * @return AccessTokenInterface|null
     */
    public function accessToken()
    {
        return $this->accessToken;
    }
}