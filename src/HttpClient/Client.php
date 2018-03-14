<?php

namespace Mel\HttpClient;

use Http\Client\HttpClient;
use Mel\Http\Responses\Response;
use Http\Discovery\MessageFactoryDiscovery;
use Psr\Http\Message\RequestInterface;

class Client implements ClientInterface
{
    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var \Http\Message\MessageFactory
     */
    protected $requestFactory;

    /**
     * Client constructor.
     *
     * @param Builder $builder
     */
    public function __construct(Builder $builder)
    {
        $this->httpClient = $builder->getHttpClient();

        $this->requestFactory = MessageFactoryDiscovery::find();
    }

    /**
     * @inheritdoc
     */
    public function sendRequest(RequestInterface $request)
    {
        $rawResponse = $this->httpClient->sendRequest($request);

        return new Response($rawResponse);
    }

    /**
     * @inheritdoc
     */
    public function send($method, $uri, array $headers = [], $body = null)
    {
        $method = mb_strtoupper($method);
        $body = is_array($body) ? json_encode($body) : $body;

        return $this->sendRequest($this->requestFactory->createRequest(
            $method,
            $uri,
            [],
            $body
        ));
    }


}