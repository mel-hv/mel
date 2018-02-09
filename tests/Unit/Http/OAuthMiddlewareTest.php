<?php

namespace MelTests\Unit\Http;

use Mel\Auth\AccessToken;
use Mel\Auth\OAuthClient;
use Mel\MeLiApp;
use MelTests\Unit\Fixtures\FooBarRequest;
use Mockery;
use Mel\Http\OAuthMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class OAuthMiddlewareTest extends TestCase
{
    protected function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testShouldAddTokenToRequestUri()
    {
        $accessTokenString = 'APP_USR-6092-3246532-cb45c82853f6e620bb0deda096b128d3-8035443';

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
            ->andReturn($accessTokenString);

        $oAuthClient->shouldReceive('refreshAccessToken')
            ->once()
            ->withNoArgs();

        $finalRequest = $oAuthMiddleware(new FooBarRequest());

        $this->assertInstanceOf(RequestInterface::class, $finalRequest);
        $this->assertEquals(
            sprintf('https://api.mercadolibre.com/?access_token=%1$s', $accessTokenString),
            $finalRequest->getUri()->__toString()
        );
    }
}
