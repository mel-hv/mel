<?php

namespace MelTests\Unit\Http\Responses;

use Mel\Http\Responses\ErrorResponse;
use MelTests\TestCase;

class ErrorResponseTest extends TestCase
{
    public function testGetErrorResponseData()
    {
        $bodyJson = $this->getJsonFileContent('error-response.json');

        $bodyResponse = json_decode($bodyJson);
        $response = new ErrorResponse($this->createResponse($bodyJson, 502));

        $this->assertEquals($bodyResponse->message, $response->getMessageError());
        $this->assertEquals($bodyResponse->error, $response->getErrorId());
        $this->assertEquals($bodyResponse->status, $response->getErrorStatus());
        $this->assertEquals($bodyResponse->cause, $response->getErrorCause());
    }
}
