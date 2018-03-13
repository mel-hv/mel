<?php

namespace MelTests\Unit\HttpClients;

use Mel\HttpClient\Builder;
use Mel\HttpClient\Client;
use Mel\Http\Responses\ResponseInterface;
use Http\Mock\Client as MockClient;
use Mockery;
use MelTests\TestCase;

class ClientTest extends TestCase
{

    public function testSendValidRequest()
    {
        $builderClient = Mockery::mock(Builder::class);
        $mockClient = new MockClient();

        $builderClient->shouldReceive('getHttpClient')
            ->once()
            ->withNoArgs()
            ->andReturn($mockClient);

        $rawResponse = $this->createResponse(['message' => 'This is a simple http response']);
        $mockClient->setDefaultResponse($rawResponse);

        $httpClient = new Client($builderClient);

        $request = $this->createRequest('POST', '/');
        $response = $httpClient->sendRequest($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('This is a simple http response', $response->message);
    }

    public function testCreateAndSendValidRequest()
    {
        $builderClient = Mockery::mock(Builder::class);
        $mockClient = new MockClient();

        $builderClient->shouldReceive('getHttpClient')
            ->once()
            ->withNoArgs()
            ->andReturn($mockClient);

        $rawResponse = $this->createResponse(['message' => 'This is a simple http response']);
        $mockClient->setDefaultResponse($rawResponse);

        $httpClient = new Client($builderClient);


        $response = $httpClient->send('post', '/', ['name' => 'message']);
        $requests = $mockClient->getRequests();


        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('This is a simple http response', $response->message);

        $this->assertEquals('POST', $requests[0]->getMethod());
        $this->assertEquals('/', $requests[0]->getUri()->getPath());
        $this->assertEquals('{"name":"message"}', $requests[0]->getBody()->__toString());
    }
}
