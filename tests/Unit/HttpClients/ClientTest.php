<?php

namespace MelTests\Unit\HttpClients;

use Mel\HttpClient\Builder;
use Mel\HttpClient\ApiClient;
use Mel\Http\Responses\ResponseInterface;
use MelTests\TestCase;

class ClientTest extends TestCase
{

    public function testSendValidRequest()
    {
        $rawResponse = $this->createResponse(['message' => 'This is a simple http response']);
        $this->mockClient->setDefaultResponse($rawResponse);

        $builderClient = new Builder($this->mockClient);

        $httpClient = new ApiClient($builderClient);

        $request = $this->createRequest('POST', '/');
        $response = $httpClient->sendRequest($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('This is a simple http response', $response->message);
    }

    public function testCreateAndSendValidRequest()
    {
        $rawResponse = $this->createResponse(['message' => 'This is a simple http response']);
        $this->mockClient->setDefaultResponse($rawResponse);

        $httpClient = new ApiClient(new Builder($this->mockClient));


        $response = $httpClient->send('post', '/', [], ['name' => 'message']);
        $requests = $this->mockClient->getRequests();


        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('This is a simple http response', $response->message);

        $this->assertEquals('POST', $requests[0]->getMethod());
        $this->assertEquals('/', $requests[0]->getUri()->getPath());
        $this->assertEquals('{"name":"message"}', $requests[0]->getBody()->__toString());
    }
}
