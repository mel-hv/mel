<?php

namespace MelTests\Unit\Fixtures\Responses;

use GuzzleHttp\Psr7\Response;

abstract class FooBaseResponse extends Response
{
    const BODY_ARRAY_FORMAT = [];

    /**
     * @var int Http status code
     */
    protected $statusCode = 202;

    /**
     * FooBaseResponse constructor.
     */
    public function __construct()
    {

        $body = json_encode(static::BODY_ARRAY_FORMAT);

        parent::__construct(
            $this->statusCode,
            [],
            $body,
            '1.1',
            null
        );
    }


}