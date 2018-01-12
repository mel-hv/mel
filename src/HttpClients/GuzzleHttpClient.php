<?php

namespace Mel\HttpClients;

use GuzzleHttp\Client;
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
        $response = $this->guzzleClient->send($request);


        return $response;
    }
}