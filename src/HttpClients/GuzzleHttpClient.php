<?php

namespace Mel\HttpClients;

use GuzzleHttp\Client;
use Mel\Http\ErrorResponse;
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
}