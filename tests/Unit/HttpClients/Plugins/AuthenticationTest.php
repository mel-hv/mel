<?php

namespace MelTests\Unit\HttpClients\Plugins;

use Mel\Auth\OAuthClient;
use Mel\HttpClient\Plugins\Authentication;
use Mel\MeLiApp;
use Psr\Http\Message\RequestInterface;
use Mel\Auth\AccessTokenInterface;
use MelTests\TestCase;

class AuthenticationTest extends TestCase
{
    public function testIfRequestNotIsAnonymousShouldAddTokenToUri()
    {
        $meLiApp = \Mockery::mock(MeLiApp::class);
        $accessToken = \Mockery::mock(AccessTokenInterface::class);
        $oAuthClient = \Mockery::mock(OAuthClient::class);

        $this->melMock->shouldReceive('meLiApp')
            ->once()
            ->withNoArgs()
            ->andReturn($meLiApp);

        $this->melMock->shouldReceive('accessToken')
            ->once()
            ->withNoArgs()
            ->andReturn($accessToken);

        $this->melMock->shouldReceive('oAuthClient')
            ->once()
            ->withNoArgs()
            ->andReturn($oAuthClient);

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


        $authentication = new Authentication($this->melMock);

        $request = $authentication->authenticate(
            $this->createRequest('GET', 'https://api.mercadolibre.com/questions/search?item_id=MLA608007087')
        );

        $this->assertInstanceOf(RequestInterface::class, $request);
        $this->assertEquals(
            sprintf(
                '%1$s/questions/search?item_id=MLA608007087&access_token=%2$s',
                trim($this->apiUri, '/'),
                $this->accessToken
            ),
            $request->getUri()->__toString()
        );
    }
}
