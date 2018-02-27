<?php

namespace MelTests\Unit\Fixtures;

use GuzzleHttp\Psr7\Response;

class FooErrorResponse extends Response
{
    const BODY_ARRAY_FORMAT = [
        'message' => 'This is a message error',
        'error'   => 'error_id',
        'status'  => 502,
        'cause'   => [
            'first cause',
            'second cause',
        ],
    ];

    /**
     * FooResponse constructor.
     */
    public function __construct()
    {
        $body = json_encode(self::BODY_ARRAY_FORMAT);

        parent::__construct(
            502,
            [],
            $body,
            '1.1',
            null
        );
    }
}