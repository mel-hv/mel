<?php

namespace MelTests\Unit\Http;

use Mockery;
use Mel\Http\ErrorResponse;
use PHPUnit\Framework\TestCase;
use MelTests\Unit\Fixtures\FooBarErrorResponse;

class ErrorResponseTest extends TestCase
{
    /**
     * @inheritDoc
     */
    protected function tearDown()
    {
        parent::tearDown();
        Mockery::close();
    }

    public function testBuildErrorResponseUsingRawResponse()
    {
        $response = new ErrorResponse(new FooBarErrorResponse());

        $this->assertTrue($response->hasErrors());
        $this->assertEquals(FooBarErrorResponse::BODY_ARRAY_FORMAT['message'], $response->getMessageError());
        $this->assertEquals(FooBarErrorResponse::BODY_ARRAY_FORMAT['error'], $response->getErrorId());
        $this->assertEquals(FooBarErrorResponse::BODY_ARRAY_FORMAT['status'], $response->getErrorStatus());
        $this->assertEquals(FooBarErrorResponse::BODY_ARRAY_FORMAT['cause'], $response->getErrorCause());
    }
}
