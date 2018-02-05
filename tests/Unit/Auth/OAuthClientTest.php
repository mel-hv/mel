<?php

namespace MelTests\Unit\Auth;

use Mel\Auth\OAuthClient;
use Mel\Country;
use Mel\Http\Response;
use Mel\HttpClients\ClientInterface;
use Mel\Mel;
use Mel\MeLiApp;
use MelTests\Unit\Fixtures\FooBarOAuthResponse;
use MelTests\Unit\Fixtures\FooResponse;
use PHPUnit\Framework\TestCase;
use Mockery;

class OAuthClientTest extends TestCase
{
    protected function tearDown()
    {
        parent::tearDown();
        Mockery::close();
    }


    public function testReturnLinkUsedToAuthorizeUser()
    {
        $clientId = 'MyAppId';
        $redirectUri = 'my-redirect-uri.com';

        $mel = Mockery::mock(Mel::class);
        $meLiApp = Mockery::mock(MeLiApp::class);
        $country = Mockery::mock(Country::class);

        $mel->shouldReceive('meLiApp')
            ->once()
            ->withNoArgs()
            ->andReturn($meLiApp);

        $mel->shouldReceive('country')
            ->once()
            ->withNoArgs()
            ->andReturn($country);

        $meLiApp->shouldReceive('clientId')
            ->once()
            ->withNoArgs()
            ->andReturn($clientId);

        $meLiApp->shouldReceive('redirectUri')
            ->once()
            ->withNoArgs()
            ->andReturn($redirectUri);

        $country->shouldReceive('id')
            ->once()
            ->withNoArgs()
            ->andReturn('MLB');

        $oAuthClient = new OAuthClient($mel);

        $expected = sprintf(
            'https://auth.mercadolivre.com.br/authorization?response_type=code&client_id=%1$s&redirect_uri=%2$s',
            $clientId,
            $redirectUri
        );

        $this->assertEquals($expected, $oAuthClient->getOAuthUri()->__toString());
    }

    // TODO: add save access token test
    public function testGetAccessTokenUsingReceivedCodeAndSaveAccessTokenData()
    {
        $code = 'this-is-a-code';

        $clientId = 'MyAppId';
        $secretKey = 'secret-key';
        $redirectUri = 'my-redirect-uri.com';

        $mel = Mockery::mock(Mel::class);
        $meLiApp = Mockery::mock(MeLiApp::class);
        $httpClient = Mockery::mock(ClientInterface::class);

        $mel->shouldReceive('meLiApp')
            ->once()
            ->withNoArgs()
            ->andReturn($meLiApp);

        $mel->shouldReceive('httpClient')
            ->once()
            ->withNoArgs()
            ->andReturn($httpClient);

        $meLiApp->shouldReceive('clientId')
            ->once()
            ->withNoArgs()
            ->andReturn($clientId);

        $meLiApp->shouldReceive('secretKey')
            ->once()
            ->withNoArgs()
            ->andReturn($secretKey);

        $meLiApp->shouldReceive('redirectUri')
            ->once()
            ->withNoArgs()
            ->andReturn($redirectUri);

        $httpClient->shouldReceive('sendRequest')
            ->once()
            ->with('POST', '/oauth/token', [
                "grant_type"    => "authorization_code",
                "client_id"     => $clientId,
                "client_secret" => $secretKey,
                "redirect_uri"  => $redirectUri,
                "code"          => $code,
            ])
            ->andReturn(new FooBarOAuthResponse());

        $oAuthClient = new OAuthClient($mel);

        $response = $oAuthClient->authorize($code);

        $this->assertInstanceOf(Response::class, $response);
    }
}
