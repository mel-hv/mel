<?php

namespace MelTests\Unit\HttpClients;

use Mockery;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
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

        $response = Mockery::mock(ResponseInterface::class);

        $client = new GuzzleHttpClient($guzzleClient);

        $guzzleClient->shouldReceive('send')
            ->once()
            ->with(Mockery::type(RequestInterface::class))
            ->andReturn($response);

        // Act
        $result = $client->sendRequest($request);

        // Assets
        $this->assertInstanceOf(ResponseInterface::class, $result);
    }
}
