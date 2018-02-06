<?php

namespace MelTests\Unit\Http\Responses;

use Mel\Http\Responses\OAuthResponse;
use MelTests\Unit\Fixtures\FooBarOAuthResponse;
use PHPUnit\Framework\TestCase;

class OAuthResponseTest extends TestCase
{
    public function testResponseReturnTokenData()
    {
        $accessToken = 'APP_USR-6092-3246532-cb45c82853f6e620bb0deda096b128d3-8035443';
        $tokenType = 'bearer';
        $expiresIn = 10800;
        $refreshToken = 'TG-5005b6b3e4b07e60756a3353';
        $scope = ['write', 'read', 'offline_access'];

        $oAuthResponse = new OAuthResponse(new FooBarOAuthResponse());

        $this->assertEquals($accessToken, $oAuthResponse->accessToken());
        $this->assertEquals($tokenType, $oAuthResponse->tokenType());
        $this->assertEquals($expiresIn, $oAuthResponse->expiresIn());
        $this->assertEquals($refreshToken, $oAuthResponse->refreshToken());
        $this->assertEquals($scope, $oAuthResponse->scope());
    }
}
