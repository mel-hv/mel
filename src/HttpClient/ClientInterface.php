<?php

namespace Mel\HttpClient;

use Psr\Http\Message\RequestInterface;
use Mel\Http\Responses\Response;

interface ClientInterface
{
    /**
     * Send an HTTP request.
     *
     * @param RequestInterface $request Request to send
     *
     * @return \Mel\Http\Responses\Response
     */
    public function sendRequest(RequestInterface $request);

    /**
     * Build request and send
     *
     * @param string     $method
     * @param string     $endpoint
     * @param array|null $params
     * @return Response
     */
    public function send($method, $endpoint, array $params = null);
}