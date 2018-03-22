<?php

namespace Mel\HttpClient;

use Http\Client\HttpClient;
use Http\Discovery\MessageFactoryDiscovery;
use Mel\Http\Responses\ResponseFactory;
use Psr\Http\Message\RequestInterface;

class ApiClient implements ClientInterface
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
        $psrResponse = $this->httpClient->sendRequest($request);

        return ResponseFactory::create($psrResponse, $request);
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