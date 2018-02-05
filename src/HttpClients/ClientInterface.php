<?php

namespace Mel\HttpClients;

use Psr\Http\Message\RequestInterface;
use Mel\Http\Response;

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

    /**
     * Build request and send
     *
     * @param string     $method
     * @param string     $endpoint
     * @param array|null $params
     * @param array      $options
     * @return Response
     */
    public function sendRequest($method, $endpoint, array $params = null, $options = []);
}