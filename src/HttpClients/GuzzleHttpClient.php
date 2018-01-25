<?php

namespace Mel\HttpClients;

use GuzzleHttp\Client;
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
            return new ErrorResponse($response);
        }

        return $response;
    }

    public function get($endpoint)
    {
        $request = new Request('GET', $endpoint);

        return $this->sendRequest($request);
    }

    public function post($endpoint, array $params)
    {
        $body = \GuzzleHttp\json_encode($params);
        $request = new Request('POST', $endpoint, [], $body);

        return $this->sendRequest($request);
    }
}