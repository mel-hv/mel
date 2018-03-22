<?php

namespace Mel\Http\Responses;

interface ResponseInterface
{
    /**
     * Return http response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function psrResponse();

    /**
     * Return original request sent
     *
     * @return null|\Psr\Http\Message\RequestInterface
     */
    public function psrRequest();

    /**
     * Gets the response status code.
     *
     * @return int Status code.
     */
    public function getStatusCode();

    /**
     * Gets the body of the message.
     *
     * @return \Psr\Http\Message\StreamInterface Returns the body as a stream.
     */
    public function getBody();

    /**
     * Get all json or a item value
     *
     * Return an object containing  the contents of the body
     * Or return a single value
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($key = null, $default = null);
}