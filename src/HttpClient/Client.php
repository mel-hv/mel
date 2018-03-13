<?php

namespace Mel\HttpClient;

use Mel\Http\Responses\Response;
use Http\Discovery\MessageFactoryDiscovery;
use Psr\Http\Message\RequestInterface;

class Client implements ClientInterface
{
    /**
     * @var Builder A http client builder
     */
    protected $builderClient;

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
        $this->builderClient = $builder;

        $this->requestFactory = MessageFactoryDiscovery::find();
    }

    /**
     * @inheritdoc
     */
    public function sendRequest(RequestInterface $request)
    {
        $rawResponse = $this->builderClient->getHttpClient()->sendRequest($request);

        return new Response($rawResponse);
    }

    /**
     * @inheritdoc
     */
    public function send($method, $endpoint, array $params = null)
    {
        $method = mb_strtoupper($method);
        $body = is_array($params) ? json_encode($params) : null;
        $request = $this->requestFactory->createRequest($method, $endpoint, [], $body);

        return $this->sendRequest($request);
}}