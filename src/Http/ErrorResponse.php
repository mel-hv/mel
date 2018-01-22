<?php

namespace Mel\Http;

class ErrorResponse extends Response
{
    /**
     * Json response is error message
     * @return bool
     */
    public function hasErrors()
    {
        return !is_null($this->getBodyItem('error'));
    }

    /**
     * Get error message
     * @return mixed
     */
    public function getMessageError()
    {
        return $this->getBodyItem('message');
    }

    /**
     * Get id error
     * @return mixed
     */
    public function getErrorId()
    {
        return $this->getBodyItem('error');
    }

    /**
     * Get status code used to identify error
     * @return mixed
     */
    public function getErrorStatus()
    {
        return $this->getBodyItem('status');
    }

    /**
     * Get error cause sent by Mercado Libre
     * @return mixed
     */
    public function getErrorCause()
    {
        return $this->getBodyItem('cause');
    }
}