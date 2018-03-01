<?php

namespace MelTests\Unit\Http\Responses;

use Mel\Http\Responses\Response;
use MelTests\TestCase;
use MelTests\Unit\Fixtures\Responses\FooBarResponse;

class ResponseTest extends TestCase
{
    public function testShouldBuildFinalResponseUsingRawResponse()
    {
        $responseBody = (object)FooBarResponse::BODY_ARRAY_FORMAT;

        $response = new Response(new FooBarResponse());

        $this->assertEquals(FooBarResponse::BODY_ARRAY_FORMAT, $response->getDecodedBody());
        $this->assertEquals($responseBody->message, $response->getBodyItem('message'));
        $this->assertEquals($responseBody->status, $response->getBodyItem('status'));
    }
}
