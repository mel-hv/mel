<?php

namespace Mel\Http\Responses;

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Http\Message\RequestInterface as PsrRequestInterface;

class Response implements ResponseInterface
{
    /**
     * @var PsrResponseInterface
     */
    protected $psrResponse;

    /**
     * @var null|PsrRequestInterface
     */
    protected $psrRequest;

    /**
     * @var array The decoded body of the http response
     */
    protected $decodedBody;

    /**
     * Response constructor.
     *
     * @param PsrResponseInterface     $psrResponse Raw response used to build final response object
     * @param PsrRequestInterface|null $psrRequest
     */
    public function __construct(PsrResponseInterface $psrResponse, PsrRequestInterface $psrRequest = null)
    {
        $this->psrResponse = $psrResponse;

        $this->psrRequest = $psrRequest;

        $this->decodedBody = json_decode($this, true);
    }

    /**
     * @inheritdoc
     */
    public function psrResponse()
    {
        return $this->psrResponse;
    }

    /**
     * @inheritdoc
     */
    public function psrRequest()
    {
        return $this->psrRequest;
    }

    /**
     * @inheritdoc
     */
    public function getStatusCode()
    {
        return $this->psrResponse->getStatusCode();
    }

    /**
     * @inheritDoc
     */
    public function getBody()
    {
        return $this->psrResponse->getBody();
    }

    /**
     * @inheritdoc
     */
    public function get($key = null, $default = null)
    {
        if (!$key) {
            return (object)$this->decodedBody;
        }

        return (array_key_exists($key, $this->decodedBody)) ? $this->decodedBody[$key] : $default;
    }

    /**
     * Return string of the body
     *
     * @return string
     */
    public function __toString()
    {
        return $this->psrResponse()->getBody()->__toString();
    }

    /**
     * Return data from response json
     *
     * @param $name string
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }
}