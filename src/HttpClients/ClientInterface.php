<?php

namespace Mel\HttpClients;

use Psr\Http\Message\RequestInterface;

interface ClientInterface
{
    /**
     * Send an HTTP request.
     *
     * @param RequestInterface $request Request to send
     *
     * @return \Mel\Http\Response
     * @throws \Mel\Exceptions\ResponseException
     */
    public function sendRequest(RequestInterface $request);
}