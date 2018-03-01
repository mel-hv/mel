<?php

namespace MelTests\Unit\Fixtures\Responses;

class FooBarResponse extends FooBaseResponse
{
    const BODY_ARRAY_FORMAT = [
        'message' => 'This is a simple message',
        'status'  => 202,
    ];
}