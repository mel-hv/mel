<?php

namespace Mel\Http\Responses;

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

class Response implements ResponseInterface
{
    /**
     * @var PsrResponseInterface
     */
    protected $httpData;

    /**
     * @var array The decoded body of the http response
     */
    protected $decodedBody;

    /**
     * Response constructor.
     *
     * @param PsrResponseInterface $rawResponse Raw response used to build final response object
     */
    public function __construct(PsrResponseInterface $rawResponse)
    {
        $this->httpData = $rawResponse;

        $this->decodedBody = json_decode($this, true);
    }

    /**
     * @inheritdoc
     */
    public function http()
    {
        return $this->httpData;
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
        return $this->http()->getBody()->__toString();
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