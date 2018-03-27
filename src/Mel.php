<?php

namespace Mel;

use Mel\HttpClient\Builder as BuilderClient;
use Mel\Auth\AccessTokenInterface;
use Mel\Auth\AccessToken;
use Mel\Auth\OAuthClient;
use Mel\Auth\Storage\SessionStorage;
use Mel\Exceptions\MelException;
use Mel\Http\UriGenerator;

class Mel
{
    const VERSION = '0.2.0';

    /**
     * @var MeLiApp App configurations instance
     */
    protected $meLiApp;

    /**
     * @var Country Country object
     */
    protected $country;

    /**
     * @var BuilderClient Http client instance
     */
    protected $builderClient;

    /**
     * @var OAuthClient Client OAuth
     */
    protected $oAuthClient;

    /**
     * @var AccessToken Access token manager
     */
    protected $accessToken;

    /**
     * @var UriGenerator
     */
    protected $uriGenerator;

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

        $this->httpClient();

        if (!$this->meLiApp()->isAnonymousClient()) {
            $this->oAuthClient = new OAuthClient($this);
        }

        $this->accessToken = $accessToken ?: new AccessToken(new SessionStorage());

        $this->uriGenerator = new UriGenerator($this);
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
     * @param BuilderClient|null $builder
     *
     * @return \Http\Client\Common\HttpMethodsClient
     */
    public function httpClient(BuilderClient $builder = null)
    {
        if ($builder || !$this->builderClient) {
            $this->builderClient = $builder ?: BuilderClient::create($this);
        }

        return $this->builderClient->getHttpClient();
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

    /**
     * Return UriGenerator
     *
     * @return UriGenerator
     */
    public function uriGenerator()
    {
        return $this->uriGenerator;
    }
}