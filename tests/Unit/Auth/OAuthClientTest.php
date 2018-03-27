<?php

namespace MelTests\Unit\Auth;

use Mel\Auth\OAuthClient;
use Mel\MeLiApp;
use Mel\HttpClient\Builder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Mockery;
use MelTests\TestCase;

class OAuthClientTest extends TestCase
{
    public function testReturnLinkUsedToAuthorizeUser()
    {
        $oAuthClient = new OAuthClient($this->getMel());

        $expected = sprintf(
            'https://auth.mercadolivre.com.br/authorization?%1$s',
            http_build_query([
                'response_type' => 'code',
                'client_id'     => $this->appId,
                'redirect_uri'  => $this->redirectUri,
            ])
        );

        $this->assertInstanceOf(UriInterface::class, $oAuthClient->getOAuthUri());
        $this->assertEquals($expected, $oAuthClient->getOAuthUri());
    }

    public function testGetAccessTokenUsingReceivedCodeAndSaveAccessTokenData()
    {
        // Arrange
        $code = 'this-is-a-code';

        $this->mockClient->addResponse(
            $this->createResponse([
                "access_token"  => "APP_USR-398483221908",
                "token_type"    => "bearer",
                "expires_in"    => 21600,
                "scope"         => "offline_access read write",
                "user_id"       => 268024640,
                "refresh_token" => "TG-5a1277bfe4b1-2640",
            ], 200)
        );

        $mel = $this->getMel();
        $builderClientTest = Builder::create($mel, $this->mockClient);
        $mel->httpClient($builderClientTest);

        // Act
        $oAuthClient = new OAuthClient($mel);
        $response = $oAuthClient->authorize($code);

        // Asserts
        $this->assertInstanceOf(ResponseInterface::class, $response);

        $request = $this->mockClient->getLastRequest();
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('/oauth/token', $request->getUri()->getPath());
    }


    /**
     * @expectedException \Mel\Exceptions\HttpResponseException
     */
//    public function testThrowExceptionIfRequestTokenReturnHttpResponseWithErrors()
//    {
//        $code = 'this-is-a-code';
//
//        $this->mockClient->addResponse(
//            $this->createResponse([
//                'message' => 'This is a message error',
//                'error'   => 'error_id',
//                'status'  => 502,
//                'cause'   => [
//                    'first cause',
//                    'second cause',
//                ],
//            ], 502)
//        );
//
//        $mel = $this->getMel();
//        $builderClientTest = Builder::create($mel, $this->mockClient);
//        $mel->httpClient($builderClientTest);
//
//
//        // Act
//        $oAuthClient = new OAuthClient($mel);
//
//        $oAuthClient->authorize($code);
//    }

    /**
     * @expectedException \Mel\Exceptions\MelException
     */
    public function testThrowExceptionsIfCodeUsedToAuthIsInvalid()
    {
        $meLiApp = Mockery::mock(MeLiApp::class);

        $this->melMock->shouldReceive('meLiApp')
            ->once()
            ->withNoArgs()
            ->andReturn($meLiApp);

        $oAuthClient = new OAuthClient($this->melMock);

        $oAuthClient->authorize();
    }

    public function testRefreshAccessToken()
    {
        // Arrange
        $code = 'this-is-a-code';

        $this->mockClient->addResponse(
            $this->createResponse([
                "access_token"  => "APP_USR-6092-3246532-cb45c82853f6e620bb0deda096b128d3-8035443",
                "token_type"    => "bearer",
                "expires_in"    => 10800,
                "refresh_token" => "TG-5005b6b3e4b07e60756a3353",
                "scope"         => "write read offline_access",
            ], 200)
        );

        $mel = $this->getMel();
        $builderClientTest = Builder::create($mel, $this->mockClient);
        $mel->httpClient($builderClientTest);

        // Act
        $oAuthClient = new OAuthClient($mel);
        $response = $oAuthClient->refreshAccessToken();

        // Asserts
        $this->assertInstanceOf(ResponseInterface::class, $response);

        $request = $this->mockClient->getLastRequest();
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('/oauth/token', $request->getUri()->getPath());
    }
}
