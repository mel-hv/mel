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
     * @param array            $options Options to configure request
     * @return \Mel\Http\Response
     */
    public function send(RequestInterface $request, $options = []);
}