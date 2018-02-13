<?php

namespace MelTests\Unit\Auth;

use Mel\Auth\AccessTokenInterface;
use Mel\Auth\OAuthClient;
use Mel\Country;
use Mel\Http\Responses\OAuthResponse;
use Mel\Http\ClientInterface;
use Mel\Mel;
use Mel\MeLiApp;
use MelTests\Unit\Fixtures\FooBarErrorResponse;
use MelTests\Unit\Fixtures\FooBarOAuthResponse;
use PHPUnit\Framework\TestCase;
use Mockery;

class OAuthClientTest extends TestCase
{
    /**
     * @var string
     */
    protected $clientId = 'MyAppId';

    /**
     * @var \Mockery\MockInterface|\Mel\Mel
     */
    protected $mel;

    /**
     * @var \Mockery\MockInterface|\Mel\MeLiApp
     */
    protected $meLiApp;

    protected function setUp()
    {
        $this->mel = Mockery::mock(Mel::class);
        $this->meLiApp = Mockery::mock(MeLiApp::class);

        $this->mel->shouldReceive('meLiApp')
            ->once()
            ->withNoArgs()
            ->andReturn($this->meLiApp);

        $this->meLiApp->shouldReceive('clientId')
            ->once()
            ->withNoArgs()
            ->andReturn($this->clientId);
    }


    protected function tearDown()
    {
        parent::tearDown();
        Mockery::close();
    }


    public function testReturnLinkUsedToAuthorizeUser()
    {
        $redirectUri = 'my-redirect-uri.com';

        $country = Mockery::mock(Country::class);

        $this->mel->shouldReceive('country')
            ->once()
            ->withNoArgs()
            ->andReturn($country);

        $this->meLiApp->shouldReceive('redirectUri')
            ->once()
            ->withNoArgs()
            ->andReturn($redirectUri);

        $country->shouldReceive('id')
            ->once()
            ->withNoArgs()
            ->andReturn('MLB');

        $oAuthClient = new OAuthClient($this->mel);

        $expected = sprintf(
            'https://auth.mercadolivre.com.br/authorization?response_type=code&client_id=%1$s&redirect_uri=%2$s',
            $this->clientId,
            $redirectUri
        );

        $this->assertEquals($expected, $oAuthClient->getOAuthUri()->__toString());
    }

    public function testGetAccessTokenUsingReceivedCodeAndSaveAccessTokenData()
    {
        $code = 'this-is-a-code';

        $secretKey = 'secret-key';
        $redirectUri = 'my-redirect-uri.com';

        $httpClient = Mockery::mock(ClientInterface::class);
        $accessToken = Mockery::mock(AccessTokenInterface::class);

        $this->mel->shouldReceive('httpClient')
            ->once()
            ->withNoArgs()
            ->andReturn($httpClient);

        $this->mel->shouldReceive('accessToken')
            ->once()
            ->withNoArgs()
            ->andReturn($accessToken);

        // MeLiApp
        $this->meLiApp->shouldReceive('secretKey')
            ->once()
            ->withNoArgs()
            ->andReturn($secretKey);

        $this->meLiApp->shouldReceive('redirectUri')
            ->once()
            ->withNoArgs()
            ->andReturn($redirectUri);

        // HttpClient
        $httpClient->shouldReceive('sendRequest')
            ->once()
            ->with('POST', '/oauth/token', [
                "grant_type"    => "authorization_code",
                "client_id"     => $this->clientId,
                "client_secret" => $secretKey,
                "redirect_uri"  => $redirectUri,
                "code"          => $code,
            ])
            ->andReturn(new FooBarOAuthResponse());

        // Access Token
        $accessToken->shouldReceive('setToken')
            ->once()
            ->with(FooBarOAuthResponse::BODY_ARRAY_FORMAT['access_token']);

        $accessToken->shouldReceive('setRefreshToken')
            ->once()
            ->with(FooBarOAuthResponse::BODY_ARRAY_FORMAT['refresh_token']);

        $accessToken->shouldReceive('setExpiresIn')
            ->once()
            ->with(FooBarOAuthResponse::BODY_ARRAY_FORMAT['expires_in']);

        // Act
        $oAuthClient = new OAuthClient($this->mel);

        $response = $oAuthClient->authorize($code);

        // Assert
        $this->assertInstanceOf(OAuthResponse::class, $response);
    }

    /**
     * @expectedException \Mel\Exceptions\ResponseException
     */
    public function testThrowExceptionIfRequestTokenReturnHttpResponseWithErrors()
    {
        $code = 'this-is-a-code';

        $secretKey = 'secret-key';
        $redirectUri = 'my-redirect-uri.com';

        $httpClient = Mockery::mock(ClientInterface::class);

        $this->mel->shouldReceive('httpClient')
            ->once()
            ->withNoArgs()
            ->andReturn($httpClient);

        // MeLiApp
        $this->meLiApp->shouldReceive('secretKey')
            ->once()
            ->withNoArgs()
            ->andReturn($secretKey);

        $this->meLiApp->shouldReceive('redirectUri')
            ->once()
            ->withNoArgs()
            ->andReturn($redirectUri);

        // HttpClient
        $httpClient->shouldReceive('sendRequest')
            ->once()
            ->with('POST', '/oauth/token', [
                "grant_type"    => "authorization_code",
                "client_id"     => $this->clientId,
                "client_secret" => $secretKey,
                "redirect_uri"  => $redirectUri,
                "code"          => $code,
            ])
            ->andReturn(new FooBarErrorResponse());

        // Act
        $oAuthClient = new OAuthClient($this->mel);

        $oAuthClient->authorize($code);
    }

    public function testRefreshAccessToken()
    {
        $secretKey = 'secret-key';
        $refreshToken = 'TG-5005b6b3e4b07e60756a3353';

        $httpClient = Mockery::mock(ClientInterface::class);
        $accessToken = Mockery::mock(AccessTokenInterface::class);


        $this->mel->shouldReceive('httpClient')
            ->once()
            ->withNoArgs()
            ->andReturn($httpClient);

        $this->mel->shouldReceive('accessToken')
            ->twice()
            ->withNoArgs()
            ->andReturn($accessToken);

        // MeLiApp
        $this->meLiApp->shouldReceive('secretKey')
            ->once()
            ->withNoArgs()
            ->andReturn($secretKey);

        // HttpClient
        $httpClient->shouldReceive('sendRequest')
            ->once()
            ->with('POST', '/oauth/token', [
                "grant_type"    => "refresh_token",
                "client_id"     => $this->clientId,
                "client_secret" => $secretKey,
                "refresh_token" => $refreshToken,
            ])
            ->andReturn(new FooBarOAuthResponse());

        // Access Token
        $accessToken->shouldReceive('getRefreshToken')
            ->once()
            ->withNoArgs()
            ->andReturn($refreshToken);

        $accessToken->shouldReceive('setToken')
            ->once()
            ->with(FooBarOAuthResponse::BODY_ARRAY_FORMAT['access_token']);

        $accessToken->shouldReceive('setRefreshToken')
            ->once()
            ->with(FooBarOAuthResponse::BODY_ARRAY_FORMAT['refresh_token']);

        $accessToken->shouldReceive('setExpiresIn')
            ->once()
            ->with(FooBarOAuthResponse::BODY_ARRAY_FORMAT['expires_in']);

        // Act
        $oAuthClient = new OAuthClient($this->mel);

        $response = $oAuthClient->refreshAccessToken();

        // Assert
        $this->assertInstanceOf(OAuthResponse::class, $response);
    }
}
