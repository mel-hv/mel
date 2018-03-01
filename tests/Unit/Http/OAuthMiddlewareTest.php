<?php

namespace MelTests\Unit\Http;

use Mel\Http\OAuthMiddleware;
use Mel\Auth\AccessToken;
use Mel\Auth\OAuthClient;
use Mel\MeLiApp;
use MelTests\Unit\Fixtures\FooBarRequest;
use Psr\Http\Message\RequestInterface;
use MelTests\TestCase;
use Mockery;

class OAuthMiddlewareTest extends TestCase
{
    public function testShouldAddTokenToRequestUri()
    {
        $meLiApp = Mockery::mock(MeLiApp::class);

        $accessToken = Mockery::mock(AccessToken::class);

        $oAuthClient = Mockery::mock(OAuthClient::class);

        $oAuthMiddleware = new OAuthMiddleware($meLiApp, $accessToken, $oAuthClient);

        $meLiApp->shouldReceive('isAnonymousClient')
            ->once()
            ->withNoArgs()
            ->andReturn(false);

        $accessToken->shouldReceive('isValid')
            ->once()
            ->withNoArgs()
            ->andReturn(true);

        $accessToken->shouldReceive('isExpired')
            ->once()
            ->withNoArgs()
            ->andReturn(true);

        $accessToken->shouldReceive('getToken')
            ->once()
            ->withNoArgs()
            ->andReturn($this->accessToken);

        $oAuthClient->shouldReceive('refreshAccessToken')
            ->once()
            ->withNoArgs();

        $finalRequest = $oAuthMiddleware(new FooBarRequest());

        $this->assertInstanceOf(RequestInterface::class, $finalRequest);
        $this->assertEquals(
            sprintf('%1$s?access_token=%2$s', $this->apiUri, $this->accessToken),
            $finalRequest->getUri()->__toString()
        );
    }
}
