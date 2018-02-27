<?php

namespace Mel\Http;

use Mel\Mel;
use Mel\Http\Responses\Response;
use Mel\Http\Responses\ErrorResponse;
use Mel\Exceptions\HttpResponseException;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriNormalizer;
use Psr\Http\Message\RequestInterface;

class HttpClient implements ClientInterface
{
    const API_URI = "https://api.mercadolibre.com";

    /**
     * @var Mel A instance of the Mel
     */
    protected $mel;

    /**
     * @var Client The Guzzle client
     */
    protected $guzzleClient;

    /**
     * GuzzleHttpClient constructor.
     *
     * @param Mel         $mel
     * @param Client|null $guzzleClient The Guzzle client
     */
    public function __construct(Mel $mel, Client $guzzleClient = null)
    {
        $this->mel = $mel;
        $this->guzzleClient = $guzzleClient ?: new Client();
    }

    /**
     * Return instance of the Uri configured using base uri of the Mercado Libre api
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function getApiUri()
    {
        return UriNormalizer::normalize(new Uri(self::API_URI));
    }

    /**
     * Get request options
     *
     * @param $options
     *
     * @return array
     */
    protected function getRequestOptions($options)
    {
        $default = [
            'base_uri'    => $this->getApiUri(),
            'http_errors' => false,
            'stack'       => $this->resolveMiddleware(),
            'headers'     => [
                'User-Agent'   => $this->getUserAgent(),
                'Content-Type' => 'application/json',
            ],
        ];

        return array_merge($default, $options);
    }

    /**
     * Resolve Middleware dependencies
     *
     * @return HandlerStack
     */
    protected function resolveMiddleware()
    {
        $oAuthMiddleware = new OAuthMiddleware(
            $this->mel->meLiApp(),
            $this->mel->accessToken(),
            $this->mel->oAuthClient()
        );

        $stack = HandlerStack::create();
        $stack->push(Middleware::mapRequest($oAuthMiddleware), 'mel_oauth_middleware');

        return $stack;
    }

    /**
     * Return user agent
     *
     * @return string
     */
    public function getUserAgent()
    {
        return 'MEL - ' . Mel::VERSION;
    }

    /**
     * @inheritdoc
     */
    public function send(RequestInterface $request, $options = [])
    {
        $options = $this->getRequestOptions($options);

        $rawResponse = $this->guzzleClient->send($request, $options);
        $response = new Response($rawResponse);

        if (!is_null($response->getBodyItem('error'))) {
            throw new HttpResponseException(
                new ErrorResponse($response)
            );
        }

        return $response;
    }

    /**
     * @inheritdoc
     * @throws HttpResponseException
     */
    public function sendRequest($method, $endpoint, array $params = null, $options = [])
    {
        $method = mb_strtoupper($method);
        $body = is_array($params) ? \GuzzleHttp\json_encode($params) : null;

        $request = new Request($method, $endpoint, [], $body);

        return $this->send($request, $options);
    }

    /**
     * Sends a GET request
     *
     * @param string $endpoint
     * @param array  $options
     *
     * @return Response
     * @throws HttpResponseException
     */
    public function get($endpoint, $options = [])
    {
        return $this->sendRequest('GET', $endpoint, null, $options);
    }

    /**
     * Sends a POST request
     *
     * @param string $endpoint
     * @param array  $params
     * @param array  $options
     *
     * @return Response
     * @throws HttpResponseException
     */
    public function post($endpoint, array $params, $options = [])
    {
        return $this->sendRequest('POST', $endpoint, $params, $options);
    }

    /**
     * Sends a PUT request
     *
     * @param string $endpoint
     * @param array  $params
     * @param array  $options
     *
     * @return Response
     * @throws HttpResponseException
     */
    public function put($endpoint, array $params, $options = [])
    {
        return $this->sendRequest('PUT', $endpoint, $params, $options);
    }

    /**
     * Sends a DELETE request
     *
     * @param string $endpoint
     * @param array  $options
     *
     * @return Response
     * @throws HttpResponseException
     */
    public function delete($endpoint, $options = [])
    {
        return $this->sendRequest('DELETE', $endpoint, null, $options);
    }
}