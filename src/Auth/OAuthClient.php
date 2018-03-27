<?php

namespace Mel\Auth;

use Mel\Mel;
use Mel\Http\ResponseManager;
use Mel\Exceptions\HttpResponseException;
use Mel\Exceptions\MelException;
use Psr\Http\Message\ResponseInterface;

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
     * @return ResponseInterface
     * @throws HttpResponseException
     * @throws MelException
     */
    public function authorize($code = null)
    {
        $code = is_string($code) ? $code : filter_input(INPUT_GET, 'code');

        if (!$code) {
            throw new MelException('Use a valid code');
        }

        $response = $this->requestToken([
            "grant_type"    => "authorization_code",
            "client_id"     => $this->meLiApp->clientId(),
            "client_secret" => $this->meLiApp->secretKey(),
            "redirect_uri"  => $this->meLiApp->redirectUri(),
            "code"          => $code,
        ]);

        $this->saveToken($response);

        return $response;
    }

    /**
     * Refresh access token used to authorize request in Mercado Libre
     *
     * @return ResponseInterface
     * @throws HttpResponseException
     */
    public function refreshAccessToken()
    {
        $response = $this->requestToken([
            "grant_type"    => "refresh_token",
            "client_id"     => $this->meLiApp->clientId(),
            "client_secret" => $this->meLiApp->secretKey(),
            "refresh_token" => $this->mel->accessToken()->getRefreshToken(),
        ]);

        $this->saveToken($response);

        return $response;
    }

    /**
     * Send request of the access token
     *
     * @param array $params
     *
     * @return ResponseInterface
     */
    protected function requestToken(array $params)
    {
        $httpClient = $this->mel->httpClient();

        $response = $httpClient->post(self::OAUTH_ENDPOINT, [], json_encode($params));
//
//        if (!$response instanceof OAuthResponse) {
//            throw new HttpResponseException('', $response);
//        }

        return $response;
    }

    /**
     * Save access token and others important data
     *
     * @param ResponseInterface $response
     */
    protected function saveToken(ResponseInterface $response)
    {
        $response = ResponseManager::toObject($response);

        $accessToken = $this->mel->accessToken();

        $accessToken->setToken($response->access_token);
        $accessToken->setRefreshToken($response->refresh_token);
        $accessToken->setExpiresIn($response->expires_in);
    }
}