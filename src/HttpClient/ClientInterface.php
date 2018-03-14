<?php

namespace Mel\HttpClient;

use Mel\Http\Responses\Response;
use Http\Client\HttpClient;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

interface ClientInterface extends HttpClient
{
    /**
     * Create and send http requests
     *
     * @param string                            $method HTTP method to use
     * @param string|UriInterface               $uri
     * @param array                             $headers
     * @param string|array|StreamInterface|null $body
     *
     * @return Response
     */
    public function send($method, $uri, array $headers = [], $body = null);
}