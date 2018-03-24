<?php

namespace Mel\Exceptions;

use Mel\Http\Responses\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HttpResponseException extends MelException
{
    /**
     * @var RequestInterface
     */
    protected $psrRequest;

    /**
     * @var ResponseInterface
     */
    protected $psrResponse;

    /**
     * @var array Response body
     */
    protected $responseBody = [];

    /**
     * HttpResponseException constructor.
     *
     * @param string          $message
     * @param Response        $response
     * @param \Exception|null $previous
     */
    public function __construct($message, Response $response, \Exception $previous = null)
    {
        $this->psrRequest = $response->psrRequest();
        $this->psrResponse = $response->psrResponse();

        $this->responseBody = (array) $response->get();

        $message = $message ?: $this->getResponseBody('message', $message);

        parent::__construct($message, $response->getStatusCode(), $previous);
    }

    public function getResponseBody($key, $default = null)
    {
        return (array_key_exists($key, $this->responseBody)) ? $this->responseBody[$key] : $default;
    }

    /**
     * Return original http request
     *
     * @return null|\Psr\Http\Message\RequestInterface Request using psr contract
     */
    public function getPsrRequest()
    {
        return $this->psrRequest;
    }

    /**
     * Return original http response
     *
     * @return \Psr\Http\Message\ResponseInterface Response using psr contract
     */
    public function getPsrResponse()
    {
        return $this->psrResponse;
    }

    /**
     * Return error code
     *
     * @return int The code of the error, is also the HTTP status code for the error
     */
    public function getErrorStatus()
    {
        return $this->getCode();
    }

    /**
     * Return error id
     *
     * @return string|null Error id used by Mercado Libre to identify error
     */
    public function getErrorId()
    {
        return $this->getResponseBody('error');
    }

    /**
     * Return causes of the error
     *
     * @return array Causes of error pointed out by Mercado Libre
     */
    public function getErrorCauses()
    {
        return $this->getResponseBody('cause', []);
    }
}