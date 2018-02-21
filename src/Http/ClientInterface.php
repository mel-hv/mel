<?php

namespace Mel\Http;

use Psr\Http\Message\RequestInterface;
use Mel\Http\Responses\Response;

interface ClientInterface
{
    /**
     * Send an HTTP request.
     *
     * @param RequestInterface $request Request to send
     *
     * @param array            $options Options to configure request
     * @return \Mel\Http\Responses\Response
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