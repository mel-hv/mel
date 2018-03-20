<?php

namespace Mel\Http\Responses;

class ErrorResponse extends Response
{
    /**
     * Get error message
     *
     * @return string|null
     */
    public function getMessageError()
    {
        return $this->get('message');
    }

    /**
     * Get id error
     *
     * @return string|null
     */
    public function getErrorId()
    {
        return $this->get('error');
    }

    /**
     * Get status code used to identify error
     *
     * @return string|int|null
     */
    public function getErrorStatus()
    {
        return $this->get('status');
    }

    /**
     * Get error cause sent by Mercado Libre
     *
     * @return array|null
     */
    public function getErrorCause()
    {
        return $this->get('cause');
    }
}