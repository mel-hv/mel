<?php

namespace MelTests\Unit;

use Http\Client\HttpClient;
use Mel\Auth\AccessTokenInterface;
use Mel\Auth\OAuthClient;
use Mel\Country;
use Mel\HttpClient\Builder;
use Mel\Mel;
use Mel\MeLiApp;
use MelTests\TestCase;

class MelTest extends TestCase
{
    /**
     * @dataProvider classAndMethodsList
     */
    public function testShouldReturnCorrectlyInstance($className, $methodToCall)
    {
        $mel = $this->getMel();

        $this->assertInstanceOf($className, $mel->{$methodToCall}());
    }

    public function classAndMethodsList()
    {
        return [
            [MeLiApp::class, 'meLiApp'],
            [Country::class, 'country'],
            [OAuthClient::class, 'oAuthClient'],
            [AccessTokenInterface::class, 'accessToken'],
            [\Mel\Http\UriGenerator::class, 'uriGenerator'],
        ];
    }

    public function testConfigureAuthenticatedMode()
    {
        $mel = $this->getMel();

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

        $mel->setHttpClientBuilder($builderClient);
        $client2 = $mel->httpClient();

        $this->assertInstanceOf(HttpClient::class, $client1);
        $this->assertInstanceOf(HttpClient::class, $client2);

        $this->assertAttributeInstanceOf(Builder::class, 'builderClient', $mel);

        $this->assertNotSame($client1, $client2);
    }

}
