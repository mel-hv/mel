<?php

namespace MelTests\Unit\HttpClients;

use Mockery;
use Mel\Http\HttpClient;
use Mel\Http\Request;
use Mel\Mel;
use GuzzleHttp\Client;
use MelTests\Unit\Fixtures\FooBarErrorResponse;
use MelTests\Unit\Fixtures\FooResponse;
use PHPUnit\Framework\TestCase;
use Mel\Http\Responses\Response;
use Psr\Http\Message\RequestInterface;

class HttpClientTest extends TestCase
{

    protected function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function getDefaultClientOptions()
    {
        return [
            'base_uri' => (new HttpClient())->getApiUri(),
            'http_errors' => false,
            'headers'     => [
                'User-Agent'   => 'MEL - ' . Mel::VERSION,
                'Content-Type' => 'application/json',
            ],
        ];
    }

    public function testShouldSendRequests()
    {
        // Arrange
        $guzzleClient = Mockery::mock(Client::class);

        $request = Mockery::mock(RequestInterface::class);

        $melClient = new HttpClient($guzzleClient);

        $guzzleClient->shouldReceive('send')
            ->once()
            ->with(Mockery::type(RequestInterface::class), $this->getDefaultClientOptions())
            ->andReturn(new FooResponse());

        // Act
        $result = $melClient->send($request);

        // Assets
        $this->assertInstanceOf(Response::class, $result);
    }

    /**
     * @expectedException \Mel\Exceptions\ResponseException
     * @expectedExceptionMessage
     */
    public function testShouldSendRequestAndExceptionIfHasErrorMessage()
    {
        // Arrange
        $guzzleClient = Mockery::mock(Client::class);

        $request = Mockery::mock(RequestInterface::class);

        $melClient = new HttpClient($guzzleClient);

        $guzzleClient->shouldReceive('send')
            ->once()
            ->with(Mockery::type(RequestInterface::class), $this->getDefaultClientOptions())
            ->andReturn(new FooBarErrorResponse());

        // Act
        $melClient->send($request);
    }

    public function testUseHttpMethodsToSendRequest()
    {
        // Arrange
        $getRequest = null;
        $postRequest = null;
        $putRequest = null;
        $deleteRequest = null;

        $guzzleClient = Mockery::mock(Client::class);

        $melClient = new HttpClient($guzzleClient);

        $guzzleClient->shouldReceive('send')
            ->times(4)
            ->with(Mockery::type(Request::class), $this->getDefaultClientOptions())
            ->andReturnUsing(function ($request) use (&$getRequest, &$postRequest, &$putRequest, &$deleteRequest) {
                $method = $request->getMethod();

                switch ($method) {
                    case 'GET':
                        $getRequest = $request;
                        break;
                    case 'POST':
                        $postRequest = $request;
                        break;
                    case 'PUT':
                        $putRequest = $request;
                        break;
                    case 'DELETE':
                        $deleteRequest = $request;
                        break;
                }

                return new FooResponse();
            });

        // Act
        $getResponse = $melClient->get('/');
        $postResponse = $melClient->post('/', ['id' => '23', 'name' => 'Product Name']);
        $putResponse = $melClient->put('/23', ['name' => 'Product Name']);
        $deleteResponse = $melClient->delete('/23');

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

        /* Assert PUT */
        $this->assertInstanceOf(Response::class, $putResponse);
        $this->assertEquals('PUT', $putRequest->getMethod());
        $this->assertEquals('/23', $putRequest->getUri());
        $this->assertEquals('{"name":"Product Name"}', $putRequest->getBody()->getContents());

        /* Assert DELETE */
        $this->assertInstanceOf(Response::class, $deleteResponse);
        $this->assertEquals('DELETE', $deleteRequest->getMethod());
        $this->assertEquals('/23', $deleteRequest->getUri());
    }
}


