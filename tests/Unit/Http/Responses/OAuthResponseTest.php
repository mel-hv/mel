<?php

namespace MelTests\Unit\Http\Responses;

use Mel\Http\Responses\OAuthResponse;
use MelTests\Unit\Fixtures\Responses\BarOAuthResponse;
use MelTests\TestCase;

class OAuthResponseTest extends TestCase
{
    public function testResponseReturnTokenData()
    {
        $responseBody = (object)BarOAuthResponse::BODY_ARRAY_FORMAT;
        $responseBody->scope = explode(' ', $responseBody->scope);

        $oAuthResponse = new OAuthResponse(new BarOAuthResponse());

        $this->assertEquals($responseBody->access_token, $oAuthResponse->accessToken());
        $this->assertEquals($responseBody->token_type, $oAuthResponse->tokenType());
        $this->assertEquals($responseBody->expires_in, $oAuthResponse->expiresIn());
        $this->assertEquals($responseBody->refresh_token, $oAuthResponse->refreshToken());
        $this->assertEquals($responseBody->scope, $oAuthResponse->scope());
    }
}
