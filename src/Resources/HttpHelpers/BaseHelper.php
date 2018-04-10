<?php

namespace Mel\Resources\HttpHelpers;

use Http\Client\HttpClient;
use Mel\Exceptions\MelException;
use Mel\Http\UriGenerator;
use Mel\Mel;
use Psr\Http\Message\ResponseInterface;

trait BaseHelper
{
    /**
     * @var \Http\Client\Common\HttpMethodsClient
     */
    protected $httpClient;

    /**
     * @var \Mel\Http\UriGenerator;
     */
    protected $uriGenerator;

    /**
     * Create a collection of resources from psr response
     *
     * @param ResponseInterface $response
     *
     * @return \DusanKasan\Knapsack\Collection
     */
    abstract public function hydrate(ResponseInterface $response);

    public function init(Mel $mel)
    {
        $this->setHttpClient($mel->httpClient());
        $this->setUriGenerator($mel->uriGenerator());
    }

    /**
     * Configure HttpClient
     *
     * @param HttpClient $httpClient
     */
    public function setHttpClient(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Get HttpClient instance
     *
     * @return \Http\Client\Common\HttpMethodsClient
     */
    protected function httpClient()
    {
        return $this->httpClient;
    }

    /**
     * Set UriGenerator
     *
     * @param UriGenerator $uriGenerator
     */
    public function setUriGenerator(UriGenerator $uriGenerator)
    {
        $this->uriGenerator = $uriGenerator;
    }

    /**
     * Get UriGenerator instance
     *
     * @return UriGenerator
     */
    public function uriGenerator()
    {
        return $this->uriGenerator;
    }

    /**
     * Get a uri instance of the paths list
     *
     * @param string $id
     * @param array  $parameters
     * @param array  $query
     *
     * @return UriGenerator|\Psr\Http\Message\UriInterface
     * @throws MelException
     */
    protected function getPath($id, $parameters = [], $query = [])
    {
        if (!array_key_exists($id, $this->path)) {
            throw new MelException(
                sprintf('Register a uri related to %1$s', $id)
            );
        }

        $path = $this->path[$id];

        return $this->uriGenerator()->resolveEndPointPath($path, $parameters, $query);
    }
}