<?php

namespace MelTests\Unit\Http;

use Mel\Http\ResponseManager;
use MelTests\TestCase;

class ResponseManagerTest extends TestCase
{
    public function testShouldReturnArraBasedInResponse()
    {
        $rawResponse = $this->createResponse(
            '{"message": "This is a response"}'
        );

        $response = ResponseManager::toArray($rawResponse);

        $this->assertInternalType('array', $response);
        $this->assertEquals($response['message'], 'This is a response');
    }

    public function testShouldReturnObjectBasedInResponse()
    {
        $rawResponse = $this->createResponse(
            '{"message": "This is a response"}'
        );

        $response = ResponseManager::toObject($rawResponse);

        $this->assertInternalType('object', $response);
        $this->assertEquals($response->message, 'This is a response');
    }
}
