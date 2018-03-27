<?php

namespace Mel\Http;

use Psr\Http\Message\ResponseInterface;

class ResponseManager
{
    public static function toArray(ResponseInterface $response)
    {
        return json_decode($response->getBody(), true);
    }

    public static function toObject(ResponseInterface $response)
    {
        return json_decode($response->getBody());
    }
}