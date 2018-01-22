<?php

namespace MelTests\Unit\Http;

use Mockery;
use Mel\Http\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

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
        // Arrange
        $responseArrayFormat = [
            'message' => 'This is a simple message',
            'status'  => 202,
        ];

        $responseJsonFormat = json_encode($responseArrayFormat);

        $rawResponse = Mockery::mock(ResponseInterface::class);
        $streamInterface = Mockery::mock(StreamInterface::class);

        $rawResponse->shouldReceive('getStatusCode')
            ->once()
            ->withNoArgs()
            ->andReturn(202);

        $rawResponse->shouldReceive('getHeaders')
            ->once()
            ->withNoArgs()
            ->andReturn([]);

        $rawResponse->shouldReceive('getBody')
            ->once()
            ->withNoArgs()
            ->andReturn($streamInterface)
            ->byDefault();

        $rawResponse->shouldReceive('getProtocolVersion')
            ->once()
            ->withNoArgs()
            ->andReturn('1.0');

        $streamInterface->shouldReceive('getContents')
            ->once()
            ->withNoArgs()
            ->andReturn($responseJsonFormat);

        $rawResponse->shouldReceive('getBody')
            ->once()
            ->withNoArgs()
            ->andReturn($streamInterface);

        // Act
        $response = new Response($rawResponse);

        // Assert
        $this->assertAttributeEquals($responseArrayFormat, 'decodedBody', $response);
        $this->assertEquals($responseArrayFormat['message'], $response->getBodyItem('message'));
        $this->assertEquals($responseArrayFormat['status'], $response->getBodyItem('status'));
    }
}
