<?php

namespace Mel\HttpClient\Plugins;

use Mel\Mel;
use Http\Discovery\UriFactoryDiscovery;
use Http\Message\Authentication as AuthenticationBase;
use Psr\Http\Message\RequestInterface;

class Authentication implements AuthenticationBase
{
    /**
     * @var \Mel\MeLiApp
     */
    protected $meLiApp;

    /**
     * @var \Mel\Auth\AccessTokenInterface|null
     */
    protected $accessToken;

    /**
     * @var \Mel\Auth\OAuthClient
     */
    protected $oAuthClient;

    /**
     * @var \Http\Message\UriFactory
     */
    protected $uri;

    /**
     * Authentication constructor.
     *
     * @param Mel $mel
     */
    public function __construct(Mel $mel)
    {
        $this->meLiApp = $mel->meLiApp();
        $this->accessToken = $mel->accessToken();
        $this->oAuthClient = $mel->oAuthClient();

        $this->uri = UriFactoryDiscovery::find();
    }

    /**
     * @inheritDoc
     */
    public function authenticate(RequestInterface $request)
    {
        if (!$this->meLiApp->isAnonymousClient() && $this->accessToken->isValid()) {
            if ($this->accessToken->isExpired()) {
                $this->oAuthClient->refreshAccessToken();
            }

            $uri = $request->getUri();

            $query = $uri->getQuery();
            $query = explode('&', $query);
            $query[] = 'access_token' . '=' . $this->accessToken->getToken();
            $query = implode('&', $query);

            $uri = $uri->withQuery($query);

            $request = $request->withUri($uri);
        }

        return $request;
    }

}