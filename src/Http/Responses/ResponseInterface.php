<?php

namespace Mel\Http\Responses;

interface ResponseInterface
{
    /**
     * Return http response
     *
     * @return ResponseInterface
     */
    public function http();

    /**
     * Get all json or a item value
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($key = null, $default = null);
}