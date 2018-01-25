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

        $melClient = new GuzzleHttpClient($guzzleClient);

        $guzzleClient->shouldReceive('send')
            ->once()
            ->with(Mockery::type(RequestInterface::class))
            ->andReturn(new FooResponse());

        // Act
        $result = $melClient->sendRequest($request);

        // Assets
        $this->assertInstanceOf(Response::class, $result);
    }

    public function testShouldSendRequestAndReturnErrorResponseIfHasErrorMessage()
    {
        // Arrange
        $guzzleClient = Mockery::mock(Client::class);

        $request = Mockery::mock(RequestInterface::class);

        $melClient = new GuzzleHttpClient($guzzleClient);

        $guzzleClient->shouldReceive('send')
            ->once()
            ->with(Mockery::type(RequestInterface::class))
            ->andReturn(new FooBarErrorResponse());

        // Act
        $result = $melClient->sendRequest($request);

        // Assets
        $this->assertInstanceOf(ErrorResponse::class, $result);
    }

    public function testUseHttpMethodsToSendRequest()
    {
        // Arrange
        $getRequest = null;
        $postRequest = null;

        $guzzleClient = Mockery::mock(Client::class);

        $melClient = new GuzzleHttpClient($guzzleClient);

        $guzzleClient->shouldReceive('send')
            ->once()
            ->with(Mockery::on(function ($request) use (&$getRequest) {
                $getRequest = $request;
                return true;
            }))
            ->andReturn(new FooResponse());

        $guzzleClient->shouldReceive('send')
            ->once()
            ->with(Mockery::on(function ($request) use (&$postRequest) {
                $postRequest = $request;
                return true;
            }))
            ->andReturn(new FooResponse());

        // Act
        $getResponse = $melClient->get('/');
        $postResponse = $melClient->post('/', ['id' => '23', 'name' => 'Product Name']);

        // Assets
        /* Assert GET */
        $this->assertInstanceOf(Response::class, $getResponse);
        $this->assertEquals('GET', $getRequest->getMethod());
        $this->assertEquals('/', $getRequest->getUri());

        /* Assert POST */
        $this->assertInstanceOf(Response::class, $postResponse);
        $this->assertEquals('POST', $postRequest->getMethod());
        $this->assertEquals('/', $postRequest->getUri());
        $this->assertEquals('{"id":"23","name":"Product Name"}', $postRequest->getBody()->getContents());
    }
}


