<?php

namespace MelTests\Unit\Fixtures;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriNormalizer;

class FooBarRequest extends Request
{
    public function __construct()
    {
        $uri = UriNormalizer::normalize(new Uri("https://api.mercadolibre.com"));

        parent::__construct('GET', $uri, [], null, '1.1');
    }

}