<?php

namespace MelTests\Unit\Exceptions;

use Mel\Exceptions\ResponseException;
use Mel\Http\Responses\ErrorResponse;
use MelTests\Unit\Fixtures\FooBarErrorResponse;
use PHPUnit\Framework\TestCase;

class ResponseExceptionTest extends TestCase
{
    public function testUseHttpResponseToBuildException()
    {
        $responseBody = FooBarErrorResponse::BODY_ARRAY_FORMAT;

        $errorResponse = new ErrorResponse(new FooBarErrorResponse());
        $responseException = new ResponseException($errorResponse);


        $this->assertEquals($responseBody['message'], $responseException->getMessage());
        $this->assertEquals($responseBody['message'], $responseException->__toString() );
        $this->assertEquals($responseBody['error'], $responseException->getErrorId());
        $this->assertEquals($responseBody['status'], $responseException->getCode());
        $this->assertEquals($responseBody['status'], $responseException->getErrorStatus());
        $this->assertEquals($responseBody['cause'], $responseException->getErrorCause());
        $this->assertInstanceOf(ErrorResponse::class, $responseException->getHttpResponse());
    }
}
