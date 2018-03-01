<?php

namespace MelTests\Unit\HttpClients;

use Mel\Mel;
use Mel\Http\HttpClient;
use Mel\Http\Request;
use Mel\Http\Responses\Response;
use MelTests\Unit\Fixtures\Responses\FooErrorResponse;
use MelTests\Unit\Fixtures\Responses\FooBarResponse;
use Mockery;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Client;
use Psr\Http\Message\RequestInterface;
use MelTests\TestCase;

class HttpClientTest extends TestCase
{
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
        parent::setUp();

        $this->guzzleClient = Mockery::mock(Client::class);
        $this->request = Mockery::mock(RequestInterface::class);

        $this->melMock->shouldReceive('meLiApp')
            ->between(0, 4)
            ->withNoArgs()
            ->andReturn(Mockery::mock(\Mel\MeLiApp::class));

        $this->melMock->shouldReceive('accessToken')
            ->between(0, 4)
            ->withNoArgs()
            ->andReturn(Mockery::mock(\Mel\Auth\AccessTokenInterface::class));

        $this->melMock->shouldReceive('oAuthClient')
            ->between(0, 4)
            ->withNoArgs()
            ->andReturn(Mockery::mock(\Mel\Auth\OAuthClient::class));
    }

    public function testReturnApiUri()
    {
        $httpClient = new HttpClient($this->melMock, $this->guzzleClient);

        $this->assertInstanceOf(Uri::class, $httpClient->getApiUri());
        $this->assertEquals($this->apiUri, $httpClient->getApiUri()->__toString());
    }

    public function testShouldSendRequests()
    {
        // Arrange
        $httpClient = new HttpClient($this->melMock, $this->guzzleClient);

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
                    'base_uri'    => (new HttpClient($this->melMock))->getApiUri(),
                    'http_errors' => false,
                ]);

                if (count($optionsDiff) !== 0) {
                    return false;
                }


                return true;
            }))
            ->andReturn(new FooBarResponse());

        // Act
        $result = $httpClient->send($this->request);

        // Assets
        $this->assertInstanceOf(Response::class, $result);
    }

    /**
     * @expectedException \Mel\Exceptions\HttpResponseException
     * @expectedExceptionMessage
     */
    public function testShouldSendRequestAndExceptionIfHasErrorMessage()
    {
        // Arrange

        $httpClient = new HttpClient($this->melMock, $this->guzzleClient);

        $this->guzzleClient->shouldReceive('send')
            ->once()
            ->with(Mockery::type(RequestInterface::class), Mockery::type('array'))
            ->andReturn(new FooErrorResponse());

        // Act
        $httpClient->send($this->request);
    }

    /**
     * @dataProvider requestData
     */
    public function testUseHttpMethodsToSendRequest($httpMethod, $uri, $requestOptions = null)
    {
        $httpClient = new HttpClient($this->melMock, $this->guzzleClient);

        $request = null;

        $this->guzzleClient->shouldReceive('send')
            ->once()
            ->with(Mockery::type(Request::class), Mockery::type('array'))
            ->andReturnUsing(function ($rawRequest) use (&$request) {

                $request = $rawRequest;

                return new FooBarResponse();
            });

        // Act
        $method = mb_strtolower($httpMethod);

        if(is_null($requestOptions)) {
            $response = $httpClient->{$method}($uri);
        } else {
            $response = $httpClient->{$method}($uri, $requestOptions);
        }

        // Assets
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals($httpMethod, $request->getMethod());
        $this->assertEquals($uri, $request->getUri());
    }

    public function requestData()
    {
        return [
            ['GET', '/'],
            ['POST', '/', ['id' => '23', 'name' => 'Product Name']],
            ['PUT', '/42', ['name' => 'Product Name']],
            ['DELETE', '/42'],
        ];
    }
}


