<?php

namespace MelTests\Unit\Http\Responses;

use Mockery;
use Mel\Http\Responses\ErrorResponse;
use PHPUnit\Framework\TestCase;
use MelTests\Unit\Fixtures\FooErrorResponse;

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
        $response = new ErrorResponse(new FooErrorResponse());

        $this->assertTrue($response->hasErrors());
        $this->assertEquals(FooErrorResponse::BODY_ARRAY_FORMAT['message'], $response->getMessageError());
        $this->assertEquals(FooErrorResponse::BODY_ARRAY_FORMAT['error'], $response->getErrorId());
        $this->assertEquals(FooErrorResponse::BODY_ARRAY_FORMAT['status'], $response->getErrorStatus());
        $this->assertEquals(FooErrorResponse::BODY_ARRAY_FORMAT['cause'], $response->getErrorCause());
    }
}
