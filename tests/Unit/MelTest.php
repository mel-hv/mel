<?php

namespace MelTests\Unit;

use Mel\Auth\AccessTokenInterface;
use Mel\Auth\OAuthClient;
use Mel\Country;
use Mel\Mel;
use Mel\MeLiApp;
use Mel\Http\ClientInterface;
use MelTests\TestCase;
use Mockery;

class MelTest extends TestCase
{
    public function testConfigureAuthenticatedMode()
    {
        $meLiApp = Mockery::mock(MeLiApp::class);

        $meLiApp->shouldReceive('isAnonymousClient')
            ->twice()
            ->withNoArgs()
            ->andReturn(false);


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
        $meLiApp = Mockery::mock(MeLiApp::class);

        $meLiApp->shouldReceive('isAnonymousClient')
            ->once()
            ->withNoArgs()
            ->andReturn(false);

        new Mel($meLiApp);
    }

    public function testGetHttpClient()
    {
        $mel = new Mel();

        $this->assertInstanceOf(ClientInterface::class, $mel->httpClient());
    }
}
