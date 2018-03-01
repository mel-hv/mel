<?php

namespace MelTests\Unit\Fixtures\Responses;

class FooErrorResponse extends FooBaseResponse
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

    protected $statusCode = 502;
}