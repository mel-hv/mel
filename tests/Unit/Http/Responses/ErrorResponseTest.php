<?php

namespace MelTests\Unit\Http\Responses;

use Mel\Http\Responses\ErrorResponse;
use MelTests\TestCase;
use MelTests\Unit\Fixtures\Responses\FooErrorResponse;

class ErrorResponseTest extends TestCase
{
    public function testBuildErrorResponseUsingRawResponse()
    {
        $bodyResponse = (object)FooErrorResponse::BODY_ARRAY_FORMAT;
        $response = new ErrorResponse(new FooErrorResponse());

        $this->assertTrue($response->hasErrors());
        $this->assertEquals($bodyResponse->message, $response->getMessageError());
        $this->assertEquals($bodyResponse->error, $response->getErrorId());
        $this->assertEquals($bodyResponse->status, $response->getErrorStatus());
        $this->assertEquals($bodyResponse->cause, $response->getErrorCause());
    }
}
