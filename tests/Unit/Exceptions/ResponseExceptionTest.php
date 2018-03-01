<?php

namespace MelTests\Unit\Exceptions;

use Mel\Exceptions\HttpResponseException;
use Mel\Http\Responses\ErrorResponse;
use MelTests\Unit\Fixtures\Responses\FooErrorResponse;
use PHPUnit\Framework\TestCase;

class ResponseExceptionTest extends TestCase
{
    public function testUseHttpResponseToBuildException()
    {
        $responseBody = (object)FooErrorResponse::BODY_ARRAY_FORMAT;

        $errorResponse = new ErrorResponse(new FooErrorResponse());
        $responseException = new HttpResponseException($errorResponse);


        $this->assertEquals($responseBody->message, $responseException->getMessage());
        $this->assertEquals($responseBody->message, $responseException->__toString());
        $this->assertEquals($responseBody->error, $responseException->getErrorId());
        $this->assertEquals($responseBody->status, $responseException->getCode());
        $this->assertEquals($responseBody->status, $responseException->getErrorStatus());
        $this->assertEquals($responseBody->cause, $responseException->getErrorCause());
        $this->assertInstanceOf(ErrorResponse::class, $responseException->getHttpResponse());
    }
}
