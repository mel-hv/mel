<?php

namespace MelTests\Unit\Http\Responses;

use Mel\Http\Responses\Response;
use MelTests\TestCase;
use Psr\Http\Message\ResponseInterface;

class ResponseTest extends TestCase
{
    public function testShouldBuildMelResponseUsingRawResponse()
    {
        $json = '{"status":202,"message":"Simple Message"}';

        $rawResponse = $this->createResponse($json);

        $melResponse = new Response($rawResponse);

        $this->assertInstanceOf(ResponseInterface::class, $melResponse->http());
        $this->assertEquals(202, $melResponse->http()->getStatusCode());

        $this->assertEquals(202, $melResponse->get()->status);
        $this->assertEquals('Simple Message', $melResponse->get()->message);


        $this->assertEquals("Simple Message", $melResponse->get('message'));
        $this->assertEquals("Empty", $melResponse->get('messages', "Empty"));

        $this->assertEquals(202, $melResponse->status);
        $this->assertEquals('Simple Message', $melResponse->message);
        $this->assertNull($melResponse->failField);
    }
}
