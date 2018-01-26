<?php

namespace Mel;

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
     * @var ClientInterface Http client instance
     */
    protected $httpClient;

    /**
     * Mel constructor.
     * @param MeLiApp $meLiApp
     */
    public function __construct(MeLiApp $meLiApp = null)
    {
        $this->meLiApp = $meLiApp ?: new MeLiApp(MeLiApp::ANONYMOUS_MODE);
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
}