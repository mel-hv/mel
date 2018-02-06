<?php

namespace Mel\Http;

use Mel\Mel;
use Mel\Http\Responses\Response;
use Mel\Http\Responses\ErrorResponse;
use Mel\Exceptions\ResponseException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriNormalizer;
use Psr\Http\Message\RequestInterface;

class HttpClient implements ClientInterface
{
    const API_URI = "https://api.mercadolibre.com";

    /**
     * @var Client The Guzzle client
     */
    protected $guzzleClient;

    /**
     * GuzzleHttpClient constructor.
     *
     * @param Client|null $guzzleClient The Guzzle client
     */
    public function __construct(Client $guzzleClient = null)
    {
        $this->guzzleClient = $guzzleClient ?: new Client();
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
            'headers'     => [
                'User-Agent'   => $this->getUserAgent(),
                'Content-Type' => 'application/json',
            ],
        ];

        return array_merge($default, $options);
    }

    public function getApiUri()
    {
        return UriNormalizer::normalize(new Uri(self::API_URI));
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
            throw new ResponseException(
                new ErrorResponse($response)
            );
        }

        return $response;
    }

    /**
     * @inheritdoc
     * @throws ResponseException
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
     * @throws ResponseException
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
     * @throws ResponseException
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
     * @throws ResponseException
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
     * @throws ResponseException
     */
    public function delete($endpoint, $options = [])
    {
        return $this->sendRequest('DELETE', $endpoint, null, $options);
    }
}