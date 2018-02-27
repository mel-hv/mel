<?php

namespace Mel\Http;

use Mel\MeLiApp;
use Mel\Auth\AccessTokenInterface;
use Mel\Auth\OAuthClient;
use Psr\Http\Message\RequestInterface;
use GuzzleHttp\Psr7\Uri;

class OAuthMiddleware
{
    /**
     * @var MeLiApp
     */
    protected $meLiApp;

    /**
     * @var AccessToken
     */
    protected $accessToken;

    /**
     * @var OAuthClient
     */
    protected $oAuthClient;

    /**
     * OAuthMiddleware constructor.
     *
     * @param MeLiApp     $meLiApp
     * @param AccessTokenInterface $accessToken
     * @param OAuthClient $oAuthClient
     */
    public function __construct(MeLiApp $meLiApp, AccessTokenInterface $accessToken, OAuthClient $oAuthClient)
    {
        $this->meLiApp = $meLiApp;
        $this->accessToken = $accessToken;
        $this->oAuthClient = $oAuthClient;
    }

    /**
     * Modifies a request
     * Add access token in uri and return new request
     *
     * @param RequestInterface $request
     *
     * @return RequestInterface
     * @throws \Mel\Exceptions\HttpResponseException
     */
    public function __invoke(RequestInterface $request)
    {
        if (!$this->meLiApp->isAnonymousClient() && $this->accessToken->isValid()) {
            if ($this->accessToken->isExpired()) {
                $this->oAuthClient->refreshAccessToken();
            }

            $uri = $request->getUri();
            $uri = Uri::withQueryValue($uri, 'access_token', $this->accessToken->getToken());
            $request = $request->withUri($uri);
        }

        return $request;
    }
}