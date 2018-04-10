<?php

namespace Mel\Resources\HttpHelpers;

use Http\Client\HttpClient;
use Mel\Exceptions\MelException;
use Psr\Http\Message\ResponseInterface;

trait BaseHelper
{
    /**
     * Create a collection of resources from psr response
     *
     * @param ResponseInterface $response
     *
     * @return \DusanKasan\Knapsack\Collection
     */
    abstract public function hydrate(ResponseInterface $response);

    /**
     * @var \Http\Client\Common\HttpMethodsClient
     */
    protected $httpClient;

    public function setHttpClient(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @return \Http\Client\Common\HttpMethodsClient
     */
    protected function httpClient()
    {
        return $this->httpClient;
    }

    /**
     * @param $id
     *
     * @return mixed
     * @throws MelException
     */
    protected function getPath($id)
    {
        if (!array_key_exists($id, $this->path)) {
            throw new MelException(
                sprintf('Register a uri related to %1$s', $id)
            );
        }

        return $this->path[$id];
    }
}