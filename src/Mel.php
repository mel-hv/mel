<?php

namespace Mel;

use Mel\Exceptions\MelException;
use Mel\HttpClients\ClientInterface;
use Mel\HttpClients\HttpClient;

class Mel
{
    const VERSION = 0.1;

    /**
     * @var MeLiApp
     */
    protected $meLiApp;

    /**
     * @var Country
     */
    protected $country;

    /**
     * @var ClientInterface Http client instance
     */
    protected $httpClient;

    /**
     * Mel constructor.
     * @param MeLiApp      $meLiApp
     * @param Country|null $country
     * @throws \Exception
     */
    public function __construct(MeLiApp $meLiApp = null, Country $country = null)
    {
        $this->meLiApp = $meLiApp ?: new MeLiApp(MeLiApp::ANONYMOUS_MODE);
        $this->country = $country;

        if (!$this->meLiApp()->isAnonymousClient() && !($this->country instanceof Country)) {
            throw new MelException('Authenticated mode require a country');
        }

        $this->httpClient = $this->resolveHttpClient();
    }

    /**
     * Resolve HttpClient instance used for the Mel
     *
     * @return ClientInterface
     */
    protected function resolveHttpClient()
    {
        return new HttpClient();
    }

    /**
     * Return MeLiApp instance
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
    public function getHttpClient()
    {
        return $this->httpClient;
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
}