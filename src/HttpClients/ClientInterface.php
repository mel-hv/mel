<?php

namespace Mel\HttpClients;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface ClientInterface
{
    /**
     * Send an HTTP request.
     *
     * @param RequestInterface $request Request to send
     *
     * @return ResponseInterface
     */
    public function sendRequest(RequestInterface $request);
}