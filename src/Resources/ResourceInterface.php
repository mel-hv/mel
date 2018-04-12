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
}