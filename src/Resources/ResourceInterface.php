<?php

namespace Mel\Resources;

use Psr\Http\Message\ResponseInterface;

interface ResourceInterface
{
    /**
     * Create a collection of resources from psr response
     *
     * @param ResponseInterface $response
     *
     * @return \Mel\Collection\Collection
     */
    public function hydrate(ResponseInterface $response);

    /**
     * Create URI using string endpoint with variables in segments
     * Segment format e.g.: /path/{variable}
     *
     * @param string $path
     * @param array  $parameters
     * @param array  $query
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function createUri($path, array $parameters = [], array $query = []);
}