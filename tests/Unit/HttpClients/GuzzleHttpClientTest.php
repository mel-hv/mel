<?php

namespace MelTests\Unit\HttpClients;

use MelTests\Unit\Fixtures\FooBarErrorResponse;
use MelTests\Unit\Fixtures\FooResponse;
use Mockery;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Mel\Http\Response;
use Mel\Http\ErrorResponse;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Mel\HttpClients\GuzzleHttpClient;

class GuzzleHttpClientTest extends TestCase
{
    protected function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testShouldSendRequests()
    {
        // Arrange
        $guzzleClient = Mockery::mock(Client::class);

        $request = Mockery::mock(RequestInterface::class);

        $client = new GuzzleHttpClient($guzzleClient);

        $guzzleClient->shouldReceive('send')
            ->once()
            ->with(Mockery::type(RequestInterface::class))
            ->andReturn(new FooResponse());

        // Act
        $result = $client->sendRequest($request);

        // Assets
        $this->assertInstanceOf(Response::class, $result);
    }

    public function testShouldSendRequestAndReturnErrorResponseIfHasErrorMessage()
    {
        // Arrange
        $guzzleClient = Mockery::mock(Client::class);

        $request = Mockery::mock(RequestInterface::class);

//        $response = Mockery::mock(ResponseInterface::class);

        $client = new GuzzleHttpClient($guzzleClient);

        $guzzleClient->shouldReceive('send')
            ->once()
            ->with(Mockery::type(RequestInterface::class))
            ->andReturn(new FooBarErrorResponse());

        // Act
        $result = $client->sendRequest($request);

        // Assets
        $this->assertInstanceOf(ErrorResponse::class, $result);
    }
}
