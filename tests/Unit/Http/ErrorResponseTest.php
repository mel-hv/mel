<?php

namespace MelTests\Unit\Http;

use Mockery;
use Mel\Http\ErrorResponse;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class ErrorResponseTest extends TestCase
{
    /**
     * @inheritDoc
     */
    protected function tearDown()
    {
        parent::tearDown();
        Mockery::close();
    }

    public function testBuildErrorResponseUsingRawResponse()
    {
        // Arrange
        $responseArrayFormat = [
            'message' => 'This is a message error',
            'error'   => 'error_id',
            'status'  => 502,
            'cause'   => [
                'first cause',
                'second cause',
            ],
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
        $response = new ErrorResponse($rawResponse);

        // Assert
        $this->assertTrue($response->hasErrors());
        $this->assertEquals($responseArrayFormat['message'], $response->getMessageError());
        $this->assertEquals($responseArrayFormat['error'], $response->getErrorId());
        $this->assertEquals($responseArrayFormat['status'], $response->getErrorStatus());
        $this->assertEquals($responseArrayFormat['cause'], $response->getErrorCause());
    }
}
