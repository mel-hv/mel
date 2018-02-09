<?php

namespace MelTests\Unit\HttpClients;

use Mel\Mel;
use Mel\Http\HttpClient;
use Mel\Http\Request;
use Mel\Http\Responses\Response;
use PHPUnit\Framework\TestCase;
use MelTests\Unit\Fixtures\FooBarErrorResponse;
use MelTests\Unit\Fixtures\FooResponse;
use Mockery;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Client;
use Psr\Http\Message\RequestInterface;

class HttpClientTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface|\Mel\Mel
     */
    protected $mel;

    /**
     * @var \Mockery\MockInterface|\GuzzleHttp\Client
     */
    protected $guzzleClient;

    /**
     * @var \Mockery\MockInterface|\Psr\Http\Message\RequestInterface
     */
    protected $request;

    protected function setUp()
    {
        $this->mel = Mockery::mock(Mel::class);
        $this->guzzleClient = Mockery::mock(Client::class);
        $this->request = Mockery::mock(RequestInterface::class);

        $this->mel->shouldReceive('meLiApp')
            ->between(0, 4)
            ->withNoArgs()
            ->andReturn(Mockery::mock(\Mel\MeLiApp::class));

        $this->mel->shouldReceive('accessToken')
            ->between(0, 4)
            ->withNoArgs()
            ->andReturn(Mockery::mock(\Mel\Auth\AccessTokenInterface::class));

        $this->mel->shouldReceive('oAuthClient')
            ->between(0, 4)
            ->withNoArgs()
            ->andReturn(Mockery::mock(\Mel\Auth\OAuthClient::class));
    }

    protected function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testReturnApiUri()
    {
        $httpClient = new HttpClient($this->mel, $this->guzzleClient);

        $this->assertInstanceOf(Uri::class, $httpClient->getApiUri());
        $this->assertEquals('https://api.mercadolibre.com/', $httpClient->getApiUri()->__toString());
    }

    public function testShouldSendRequests()
    {
        // Arrange
        $httpClient = new HttpClient($this->mel, $this->guzzleClient);

        $this->guzzleClient->shouldReceive('send')
            ->once()
            ->with(Mockery::type(RequestInterface::class), Mockery::on(function ($options) {

                $stack = $options['stack'];
                unset($options['stack']);

                if (!$stack instanceof HandlerStack) {
                    return false;
                }

                if (preg_match('/(mel_oauth_middleware)/', $stack->__toString()) !== 1) {
                    return false;
                }

                $headers = $options['headers'];
                unset($options['headers']);

                $headersDiff = array_diff($headers, [
                    'User-Agent'   => 'MEL - ' . Mel::VERSION,
                    'Content-Type' => 'application/json',
                ]);

                if (count($headersDiff) !== 0) {
                    return false;
                }

                $optionsDiff = array_diff($options, [
                    'base_uri'    => (new HttpClient($this->mel))->getApiUri(),
                    'http_errors' => false,
                ]);

                if (count($optionsDiff) !== 0) {
                    return false;
                }


                return true;
            }))
            ->andReturn(new FooResponse());

        // Act
        $result = $httpClient->send($this->request);

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

        $httpClient = new HttpClient($this->mel, $this->guzzleClient);

        $this->guzzleClient->shouldReceive('send')
            ->once()
            ->with(Mockery::type(RequestInterface::class), Mockery::type('array'))
            ->andReturn(new FooBarErrorResponse());

        // Act
        $httpClient->send($this->request);
    }

    public function testUseHttpMethodsToSendRequest()
    {
        // Arrange
        $getRequest = null;
        $postRequest = null;
        $putRequest = null;
        $deleteRequest = null;

        $httpClient = new HttpClient($this->mel, $this->guzzleClient);

        $this->guzzleClient->shouldReceive('send')
            ->times(4)
            ->with(Mockery::type(Request::class), Mockery::type('array'))
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
        $getResponse = $httpClient->get('/');
        $postResponse = $httpClient->post('/', ['id' => '23', 'name' => 'Product Name']);
        $putResponse = $httpClient->put('/23', ['name' => 'Product Name']);
        $deleteResponse = $httpClient->delete('/23');

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


