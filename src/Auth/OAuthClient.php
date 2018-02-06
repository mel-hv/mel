<?php

namespace Mel\Auth;

use Mel\Mel;
use Mel\Country;
use Mel\Http\Responses\ErrorResponse;
use Mel\Http\Responses\OAuthResponse;
use Mel\Exceptions\ResponseException;
use GuzzleHttp\Psr7\Uri;

class OAuthClient
{
    const OAUTH_ENDPOINT = "/oauth/token";

    /**
     * Auth url list by country
     *
     * @var array
     */
    protected static $authUri = [
        Country::ARGENTINA   => "https://auth.mercadolibre.com.ar",
        Country::BOLIVIA     => "https://auth.mercadolibre.com.bo",
        Country::BRASIL      => "https://auth.mercadolivre.com.br",
        Country::CHILE       => "https://auth.mercadolibre.cl",
        Country::COLOMBIA    => "https://auth.mercadolibre.com.co",
        Country::COSTA_RICA  => "https://auth.mercadolibre.com.cr",
        Country::DOMINICANA  => "https://auth.mercadolibre.com.do",
        Country::EL_SALVADOR => "https://auth.mercadolibre.com.sv",
        Country::EQUADOR     => "https://auth.mercadolibre.com.ec",
        Country::GUATEMALA   => "https://auth.mercadolibre.com.gt",
        Country::HONDURAS    => "https://auth.mercadolibre.com.hn",
        Country::MEXICO      => "https://auth.mercadolibre.com.mx",
        Country::NICARAGUA   => "https://auth.mercadolibre.com.ni",
        Country::PANAMA      => "https://auth.mercadolibre.com.pa",
        Country::PARAGUAI    => "https://auth.mercadolibre.com.py",
        Country::PERU        => "https://auth.mercadolibre.com.pe",
        Country::PORTUGAL    => "https://auth.mercadolibre.com.pt",
        Country::URUGUAI     => "https://auth.mercadolibre.com.uy",
        Country::VENEZUELA   => "https://auth.mercadolibre.com.ve",
    ];

    /**
     * @var Mel
     */
    protected $mel;

    /**
     * OAuthClient constructor.
     *
     * @param Mel $mel
     */
    public function __construct(Mel $mel)
    {
        $this->mel = $mel;
    }


    /**
     * Get uri used to Oauth authorization
     *
     * @return Uri|\Psr\Http\Message\UriInterface
     */
    public function getOAuthUri()
    {
        $meLiApp = $this->mel->meLiApp();
        $country = $this->mel->country();

        $uri = new Uri(self::$authUri[$country->id()] . '/authorization');

        $uri = Uri::withQueryValue($uri, 'response_type', 'code');
        $uri = Uri::withQueryValue($uri, 'client_id', $meLiApp->clientId());
        $uri = Uri::withQueryValue($uri, 'redirect_uri', $meLiApp->redirectUri());

        return $uri;
    }

    /**
     * Requests access token to Mercado Libre
     *
     * @param $code
     *
     * @return OAuthResponse
     * @throws ResponseException
     */
    public function authorize($code)
    {
        $meLiApp = $this->mel->meLiApp();
        $httpClient = $this->mel->httpClient();
        $accessToken = $this->mel->accessToken();

        $rawResponse = $httpClient->sendRequest('POST', self::OAUTH_ENDPOINT, [
            "grant_type"    => "authorization_code",
            "client_id"     => $meLiApp->clientId(),
            "client_secret" => $meLiApp->secretKey(),
            "redirect_uri"  => $meLiApp->redirectUri(),
            "code"          => $code,
        ]);

        $oAuthResponse = new OAuthResponse($rawResponse);

        if ($this->responseHasErrors($oAuthResponse)) {
            throw new ResponseException(new ErrorResponse($oAuthResponse));
        }

        $accessToken->setToken($oAuthResponse->accessToken());
        $accessToken->setRefreshToken($oAuthResponse->refreshToken());
        $accessToken->setExpiresIn($oAuthResponse->expiresIn());

        return $oAuthResponse;

    }

    /**
     * Return if response has errors
     *
     * @param OAuthResponse $oAuthResponse
     *
     * @return bool
     */
    public function responseHasErrors(OAuthResponse $oAuthResponse)
    {
        return (is_null($oAuthResponse->accessToken()) || !is_null($oAuthResponse->getBodyItem('error')));
    }
}