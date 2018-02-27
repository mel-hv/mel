<?php

namespace Mel\Exceptions;

use Mel\Http\Responses\ErrorResponse;

class HttpResponseException extends MelException
{
    /**
     * @var ErrorResponse Error response instance
     */
    protected $errorResponse;

    /**
     * HttpResponseException constructor.
     * @param ErrorResponse $errorResponse
     */
    public function __construct(ErrorResponse $errorResponse)
    {
        $this->errorResponse = $errorResponse;
        parent::__construct($this->errorResponse->getMessageError(), $this->errorResponse->getErrorStatus());
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return $this->getMessage();
    }

    /**
     * Return error identify
     *
     * @return string|null
     */
    public function getErrorId()
    {
        return $this->errorResponse->getErrorId();
    }

    /**
     * Return error code status
     *
     * @return string|integer|null
     */
    public function getErrorStatus()
    {
        return $this->errorResponse->getErrorStatus();
    }

    /**
     * Return error causes
     *
     * @return array|null
     */
    public function getErrorCause()
    {
        return $this->errorResponse->getErrorCause();
    }

    /**
     * Return response instance
     *
     * @return ErrorResponse
     */
    public function getHttpResponse()
    {
        return $this->errorResponse;
    }
}