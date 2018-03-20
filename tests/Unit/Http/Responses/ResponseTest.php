<?php

namespace MelTests\Unit\Http\Responses;

use Mel\Http\Responses\Response;
use MelTests\TestCase;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

class ResponseTest extends TestCase
{
    /**
     * @var string
     */
    protected $bodyJson = '{"status":202,"message":"Simple Message"}';

    /**
     * @var \Psr\Http\Message\ResponseInterface
     */
    protected $psrResponse;

    /**
     * @var Response
     */
    protected $melResponse;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->psrResponse = $this->createResponse($this->bodyJson);

        $this->melResponse = new Response($this->psrResponse);
    }

    public function testGetOriginalResponseUsedToBuildObject()
    {
        $this->assertEquals(202, $this->melResponse->getStatusCode());
        $this->assertInstanceOf(PsrResponseInterface::class, $this->melResponse->psrResponse());
    }

    public function testShouldReturnResponseBodyValue()
    {
        $this->assertEquals($this->bodyJson, $this->melResponse->__toString());
        $this->assertEquals($this->bodyJson, $this->melResponse->getBody()->__toString());
        $this->assertInstanceOf(\stdClass::class, $this->melResponse->get());
    }

    public function testShouldReturnSingleValueOfTheBody()
    {
        $this->assertEquals(202, $this->melResponse->get()->status);
        $this->assertEquals('Simple Message', $this->melResponse->get()->message);


        $this->assertEquals("Simple Message", $this->melResponse->get('message'));
        $this->assertEquals("Empty", $this->melResponse->get('messages', "Empty"));

        $this->assertEquals(202, $this->melResponse->status);
        $this->assertEquals('Simple Message', $this->melResponse->message);

        $this->assertNull($this->melResponse->failField);
    }
}
