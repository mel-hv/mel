<?php

namespace MelTests\Unit\Fixtures;

use GuzzleHttp\Psr7\Response;

class FooBarResponse extends Response
{
    const BODY_ARRAY_FORMAT = [
        'message' => 'This is a simple message',
        'status'  => 202,
    ];

    /**
     * FooResponse constructor.
     */
    public function __construct()
    {
        parent::__construct(
            202,
            [],
            '{"message":"This is a simple message", "status": 202}',
            '1.1',
            null
        );
    }
}