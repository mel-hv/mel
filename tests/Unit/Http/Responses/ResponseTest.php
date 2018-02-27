<?php

namespace MelTests\Unit\Http\Responses;

use Mockery;
use Mel\Http\Responses\Response;
use PHPUnit\Framework\TestCase;
use MelTests\Unit\Fixtures\FooBarResponse;

class ResponseTest extends TestCase
{
    /**
     * @inheritDoc
     */
    protected function tearDown()
    {
        parent::tearDown();
        Mockery::close();
    }

    public function testShouldBuildFinalResponseUsingRawResponse()
    {
        $response = new Response(new FooBarResponse());

        $this->assertEquals(FooBarResponse::BODY_ARRAY_FORMAT, $response->getDecodedBody());
        $this->assertEquals(FooBarResponse::BODY_ARRAY_FORMAT['message'], $response->getBodyItem('message'));
        $this->assertEquals(FooBarResponse::BODY_ARRAY_FORMAT['status'], $response->getBodyItem('status'));
    }
}
