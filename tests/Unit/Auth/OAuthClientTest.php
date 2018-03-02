<?php

namespace MelTests\Unit\Auth;

use Mel\Auth\OAuthClient;
use Mel\Auth\AccessTokenInterface;
use Mel\Country;
use Mel\Http\Responses\OAuthResponse;
use Mel\Http\ClientInterface;
use Mel\MeLiApp;
use MelTests\Unit\Fixtures\Responses\FooErrorResponse;
use MelTests\Unit\Fixtures\Responses\BarOAuthResponse;
use MelTests\TestCase;
use Mockery;

class OAuthClientTest extends TestCase
{
     /**
     * @var \Mockery\MockInterface|\Mel\MeLiApp
     */
    protected $meLiApp;

    protected function setUp()
    {
        parent::setUp();

        $this->meLiApp = Mockery::mock(MeLiApp::class);

        $this->melMock->shouldReceive('meLiApp')
            ->once()
            ->withNoArgs()
            ->andReturn($this->meLiApp);
    }

    public function testReturnLinkUsedToAuthorizeUser()
    {
        $country = Mockery::mock(Country::class);

        $this->melMock->shouldReceive('country')
            ->once()
            ->withNoArgs()
            ->andReturn($country);

        $this->meLiApp->shouldReceive('clientId')
            ->once()
            ->withNoArgs()
            ->andReturn($this->appId);

        $this->meLiApp->shouldReceive('redirectUri')
            ->once()
            ->withNoArgs()
            ->andReturn($this->redirectUri);

        $country->shouldReceive('id')
            ->once()
            ->withNoArgs()
            ->andReturn('MLB');

        $oAuthClient = new OAuthClient($this->melMock);

        $expected = sprintf(
            'https://auth.mercadolivre.com.br/authorization?response_type=code&client_id=%1$s&redirect_uri=%2$s',
            $this->appId,
            $this->redirectUri
        );

        $this->assertEquals($expected, $oAuthClient->getOAuthUri()->__toString());
    }

    public function testGetAccessTokenUsingReceivedCodeAndSaveAccessTokenData()
    {
        $code = 'this-is-a-code';

        $httpClient = Mockery::mock(ClientInterface::class);
        $accessToken = Mockery::mock(AccessTokenInterface::class);

        $this->melMock->shouldReceive('httpClient')
            ->once()
            ->withNoArgs()
            ->andReturn($httpClient);

        $this->melMock->shouldReceive('accessToken')
            ->once()
            ->withNoArgs()
            ->andReturn($accessToken);

        // MeLiApp
        $this->meLiApp->shouldReceive('clientId')
            ->once()
            ->withNoArgs()
            ->andReturn($this->appId);

        $this->meLiApp->shouldReceive('secretKey')
            ->once()
            ->withNoArgs()
            ->andReturn($this->secretKey);

        $this->meLiApp->shouldReceive('redirectUri')
            ->once()
            ->withNoArgs()
            ->andReturn($this->redirectUri);

        // HttpClient
        $httpClient->shouldReceive('sendRequest')
            ->once()
            ->with('POST', '/oauth/token', [
                "grant_type"    => "authorization_code",
                "client_id"     => $this->appId,
                "client_secret" => $this->secretKey,
                "redirect_uri"  => $this->redirectUri,
                "code"          => $code,
            ])
            ->andReturn(new BarOAuthResponse());

        // Access Token
        $accessToken->shouldReceive('setToken')
            ->once()
            ->with(BarOAuthResponse::BODY_ARRAY_FORMAT['access_token']);

        $accessToken->shouldReceive('setRefreshToken')
            ->once()
            ->with(BarOAuthResponse::BODY_ARRAY_FORMAT['refresh_token']);

        $accessToken->shouldReceive('setExpiresIn')
            ->once()
            ->with(BarOAuthResponse::BODY_ARRAY_FORMAT['expires_in']);

        // Act
        $oAuthClient = new OAuthClient($this->melMock);

        $response = $oAuthClient->authorize($code);

        // Assert
        $this->assertInstanceOf(OAuthResponse::class, $response);
    }

    /**
     * @expectedException \Mel\Exceptions\HttpResponseException
     */
    public function testThrowExceptionIfRequestTokenReturnHttpResponseWithErrors()
    {
        $code = 'this-is-a-code';

        $httpClient = Mockery::mock(ClientInterface::class);

        $this->melMock->shouldReceive('httpClient')
            ->once()
            ->withNoArgs()
            ->andReturn($httpClient);

        // MeLiApp
        $this->meLiApp->shouldReceive('clientId')
            ->once()
            ->withNoArgs()
            ->andReturn($this->appId);

        $this->meLiApp->shouldReceive('secretKey')
            ->once()
            ->withNoArgs()
            ->andReturn($this->secretKey);

        $this->meLiApp->shouldReceive('redirectUri')
            ->once()
            ->withNoArgs()
            ->andReturn($this->redirectUri);

        // HttpClient
        $httpClient->shouldReceive('sendRequest')
            ->once()
            ->with('POST', '/oauth/token', [
                "grant_type"    => "authorization_code",
                "client_id"     => $this->appId,
                "client_secret" => $this->secretKey,
                "redirect_uri"  => $this->redirectUri,
                "code"          => $code,
            ])
            ->andReturn(new FooErrorResponse());

        // Act
        $oAuthClient = new OAuthClient($this->melMock);

        $oAuthClient->authorize($code);
    }

    /**
     * @expectedException \Mel\Exceptions\MelException
     */
    public function testThrowExceptionsIfCodeUsedToAuthIsInvalid()
    {
        $oAuthClient = new OAuthClient($this->melMock);

        $oAuthClient->authorize();
    }

    public function testRefreshAccessToken()
    {
        $httpClient = Mockery::mock(ClientInterface::class);
        $accessToken = Mockery::mock(AccessTokenInterface::class);

        $this->melMock->shouldReceive('httpClient')
            ->once()
            ->withNoArgs()
            ->andReturn($httpClient);

        $this->melMock->shouldReceive('accessToken')
            ->twice()
            ->withNoArgs()
            ->andReturn($accessToken);

        // MeLiApp
        $this->meLiApp->shouldReceive('clientId')
            ->once()
            ->withNoArgs()
            ->andReturn($this->appId);

        $this->meLiApp->shouldReceive('secretKey')
            ->once()
            ->withNoArgs()
            ->andReturn($this->secretKey);

        // HttpClient
        $httpClient->shouldReceive('sendRequest')
            ->once()
            ->with('POST', '/oauth/token', [
                "grant_type"    => "refresh_token",
                "client_id"     => $this->appId,
                "client_secret" => $this->secretKey,
                "refresh_token" => $this->refreshToken,
            ])
            ->andReturn(new BarOAuthResponse());

        // Access Token
        $accessToken->shouldReceive('getRefreshToken')
            ->once()
            ->withNoArgs()
            ->andReturn($this->refreshToken);

        $accessToken->shouldReceive('setToken')
            ->once()
            ->with(BarOAuthResponse::BODY_ARRAY_FORMAT['access_token']);

        $accessToken->shouldReceive('setRefreshToken')
            ->once()
            ->with(BarOAuthResponse::BODY_ARRAY_FORMAT['refresh_token']);

        $accessToken->shouldReceive('setExpiresIn')
            ->once()
            ->with(BarOAuthResponse::BODY_ARRAY_FORMAT['expires_in']);

        // Act
        $oAuthClient = new OAuthClient($this->melMock);

        $response = $oAuthClient->refreshAccessToken();

        // Assert
        $this->assertInstanceOf(OAuthResponse::class, $response);
    }
}
