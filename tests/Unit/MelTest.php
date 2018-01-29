<?php

namespace MelTests\Unit;

use Mel\Country;
use Mel\Mel;
use Mel\MeLiApp;
use Mel\HttpClients\ClientInterface;
use PHPUnit\Framework\TestCase;
use Mockery;

class MelTest extends TestCase
{
    protected function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testShouldReturnMeLiAppInstance()
    {
        $meLiApp = Mockery::mock(MeLiApp::class);

        $meLiApp->shouldReceive('isAnonymousClient')
            ->once()
            ->withNoArgs()
            ->andReturn(false);


        $mel = new Mel($meLiApp, new Country(Country::BRASIL));

        $this->assertInstanceOf(MeLiApp::class, $mel->meLiApp());
        $this->assertInstanceOf(Country::class, $mel->country());
    }

    public function testShouldConfigureAnonymousModeToClient()
    {
        $mel = new Mel();

        $this->assertInstanceOf(MeLiApp::class, $mel->meLiApp());
        $this->assertTrue($mel->meLiApp()->isAnonymousClient());
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

        $this->assertInstanceOf(ClientInterface::class, $mel->getHttpClient());
    }
}
