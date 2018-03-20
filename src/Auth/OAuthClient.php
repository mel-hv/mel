<?php

namespace Mel\Auth;

use Mel\Mel;
use Mel\Exceptions\MelException;
use Mel\Http\Responses\ErrorResponse;
use Mel\Http\Responses\OAuthResponse;
use Mel\Exceptions\HttpResponseException;

class OAuthClient
{
    const OAUTH_ENDPOINT = "/oauth/token";

    /**
     * @var Mel
     */
    protected $mel;

    /**
     * @var \Mel\MeLiApp MeLiApp instance
     */
    protected $meLiApp;

    /**
     * OAuthClient constructor.
     *
     * @param Mel $mel
     */
    public function __construct(Mel $mel)
    {
        $this->mel = $mel;
        $this->meLiApp = $mel->meLiApp();
    }


    /**
     * Get uri used to Oauth authorization
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function getOAuthUri()
    {
        $uriGenerator = $this->mel->uriGenerator();

        $uri = $uriGenerator->createOAuthUri('/authorization', [
            'response_type' => 'code',
            'client_id'     => $this->meLiApp->clientId(),
            'redirect_uri'  => $this->meLiApp->redirectUri(),
        ]);

        return $uri;
    }

    /**
     * Requests access token to Mercado Libre
     *
     * @param $code
     *
     * @return OAuthResponse
     * @throws HttpResponseException
     * @throws MelException
     */
    public function authorize($code = null)
    {
        $code = is_string($code) ? $code : filter_input(INPUT_GET, 'code');

        if (!$code) {
            throw new MelException('Use a valid code');
        }

        $oAuthResponse = $this->requestToken([
            "grant_type"    => "authorization_code",
            "client_id"     => $this->meLiApp->clientId(),
            "client_secret" => $this->meLiApp->secretKey(),
            "redirect_uri"  => $this->meLiApp->redirectUri(),
            "code"          => $code,
        ]);

        $this->saveToken($oAuthResponse);

        return $oAuthResponse;
    }

    /**
     * Refresh access token used to authorize request in Mercado Libre
     *
     * @return OAuthResponse
     * @throws HttpResponseException
     */
    public function refreshAccessToken()
    {
        $oAuthResponse = $this->requestToken([
            "grant_type"    => "refresh_token",
            "client_id"     => $this->meLiApp->clientId(),
            "client_secret" => $this->meLiApp->secretKey(),
            "refresh_token" => $this->mel->accessToken()->getRefreshToken(),
        ]);

        $this->saveToken($oAuthResponse);

        return $oAuthResponse;
    }

    /**
     * Send request of the access token
     *
     * @param array $params
     *
     * @return OAuthResponse
     * @throws HttpResponseException
     */
    protected function requestToken(array $params)
    {
        $httpClient = $this->mel->httpClient();

        $response = $httpClient->send('POST', self::OAUTH_ENDPOINT, $params);

        if (!$response instanceof OAuthResponse) {
            throw new HttpResponseException(new ErrorResponse($response->psrResponse()));
        }

        return $response;
    }

    /**
     * Save access token and others important data
     *
     * @param OAuthResponse $oAuthResponse
     */
    protected function saveToken(OAuthResponse $oAuthResponse)
    {
        $accessToken = $this->mel->accessToken();

        $accessToken->setToken($oAuthResponse->accessToken());
        $accessToken->setRefreshToken($oAuthResponse->refreshToken());
        $accessToken->setExpiresIn($oAuthResponse->expiresIn());
    }
}