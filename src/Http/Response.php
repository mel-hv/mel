<?php

namespace Mel\Http;

use GuzzleHttp\Psr7\Response as GuzzlerResponse;
use Psr\Http\Message\ResponseInterface;

class Response extends GuzzlerResponse implements ResponseInterface
{
    /**
     * @var array The decoded body of the response
     */
    protected $decodedBody;

    /**
     * Response constructor.
     * @param ResponseInterface $rawResponse Raw response used to build final response object
     * @param null              $reason      Reason phrase
     */
    public function __construct(ResponseInterface $rawResponse, $reason = null)
    {
        parent::__construct(
            $rawResponse->getStatusCode(),
            $rawResponse->getHeaders(),
            $rawResponse->getBody(),
            $rawResponse->getProtocolVersion(),
            $reason
        );

        $this->decodeBody();
    }

    /**
     * Convert the raw response into an array if possible
     */
    public function decodeBody()
    {
        $this->decodedBody = json_decode($this->getBodyJson(), true);
    }

    /**
     *  Returns the remaining contents in a string
     *
     * @return bool|string
     */
    public function getBodyJson()
    {
        return $this->getBody()->getContents();
    }

    /**
     * Get json item value
     *
     * @param string $key
     * @param mixed  $default
     * @return mixed|null
     */
    public function getBodyItem($key, $default = null)
    {
        return (array_key_exists($key, $this->decodedBody)) ? $this->decodedBody[$key] : $default;
    }
}