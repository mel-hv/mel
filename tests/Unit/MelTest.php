<?php

namespace MelTests\Unit;

use Mel\Auth\AccessTokenInterface;
use Mel\Auth\OAuthClient;
use Mel\Country;
use Mel\HttpClient\Builder;
use Mel\HttpClient\ClientInterface;
use Mel\Mel;
use Mel\MeLiApp;
use MelTests\TestCase;

class MelTest extends TestCase
{
    public function testConfigureAuthenticatedMode()
    {
        $meLiApp = new MeLiApp($this->appId, $this->secretKey, $this->redirectUri);

        $mel = new Mel($meLiApp, new Country(Country::BRASIL));

        $this->assertInstanceOf(MeLiApp::class, $mel->meLiApp());
        $this->assertInstanceOf(Country::class, $mel->country());
        $this->assertInstanceOf(OAuthClient::class, $mel->oAuthClient());
        $this->assertInstanceOf(AccessTokenInterface::class, $mel->accessToken());
    }

    public function testShouldConfigureAnonymousModeToClient()
    {
        $mel = new Mel();

        $this->assertInstanceOf(MeLiApp::class, $mel->meLiApp());
        $this->assertTrue($mel->meLiApp()->isAnonymousClient());
        $this->assertNull($mel->oAuthClient());
    }

    /**
     * @expectedException \Mel\Exceptions\MelException
     * @expectedExceptionMessage Authenticated mode require a country
     */
    public function testTrowExceptionIfItIsNotAnonymousModeAndNotHasConfiguredCountry()
    {
        $meLiApp = new MeLiApp($this->appId, $this->secretKey, $this->redirectUri);

        new Mel($meLiApp);
    }

    public function testShouldCreateNewInstanceOfTheHttpClientAndReturnIt()
    {
        $builderClient = \Mockery::mock(Builder::class);

        $builderClient->shouldReceive('getHttpClient')
            ->once()
            ->withNoArgs()
            ->andReturn($this->mockClient);

        $mel = new Mel();

        $client1 = $mel->httpClient();

        $client2 = $mel->httpClient($builderClient);

        $this->assertInstanceOf(ClientInterface::class, $client1);
        $this->assertInstanceOf(ClientInterface::class, $client2);

        $this->assertNotSame($client1, $client2);
    }
}
