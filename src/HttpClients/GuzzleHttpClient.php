<?php

namespace Mel\HttpClients;

use GuzzleHttp\Client;
use Mel\Exceptions\ResponseException;
use Mel\Http\ErrorResponse;
use Mel\Http\Request;
use Mel\Http\Response;
use Psr\Http\Message\RequestInterface;

class GuzzleHttpClient implements ClientInterface
{
    /**
     * @var Client The Guzzle client
     */
    protected $guzzleClient;

    /**
     * GuzzleHttpClient constructor.
     * @param Client|null $guzzleClient The Guzzle client
     */
    public function __construct(Client $guzzleClient = null)
    {
        $this->guzzleClient = $guzzleClient ?: new Client();
    }

    /**
     * @inheritdoc
     */
    public function sendRequest(RequestInterface $request)
    {
        $rawResponse = $this->guzzleClient->send($request);
        $response = new Response($rawResponse);

        if (!is_null($response->getBodyItem('error'))) {
            throw new ResponseException(
                new ErrorResponse($response)
            );
        }

        return $response;
    }

    /**
     * Sends a GET request
     *
     * @param $endpoint
     * @return Response
     * @throws ResponseException
     */
    public function get($endpoint)
    {
        $request = new Request('GET', $endpoint);

        return $this->sendRequest($request);
    }

    /**
     * Sends a POST request
     *
     * @param       $endpoint
     * @param array $params
     * @return Response
     * @throws ResponseException
     */
    public function post($endpoint, array $params)
    {
        $body = \GuzzleHttp\json_encode($params);
        $request = new Request('POST', $endpoint, [], $body);

        return $this->sendRequest($request);
    }
}