<?php

namespace MelTests\Unit\Http\Responses;

use Mel\Http\Responses\ResponseFactory;
use Mel\Http\Responses\Response;
use Mel\Http\Responses\OAuthResponse;
use Mel\Http\Responses\ErrorResponse;
use MelTests\TestCase;

class ResponseFactoryTest extends TestCase
{
    public function testShouldCreateDefaultResponseIfOriginalResponseNotHasEspcialData()
    {
        $psrResponse = $this->createResponse(
            '{"status":202,"message":"Simple Message"}'
        );

        $response = ResponseFactory::create($psrResponse);

        $this->assertInstanceOf(Response::class, $response);
    }


    public function testShouldCreateAOAuthResponseIfJsonHasAuthenticationData()
    {
        $psrResponse = $this->createResponse(
            $this->getJsonFileContent('oauth-response.json')
        );

        $response = ResponseFactory::create($psrResponse);

        $this->assertInstanceOf(OAuthResponse::class, $response);
    }

    public function testShouldCreateAErrorResponseIfJsonHasErrorStatus()
    {
        $psrResponse = $this->createResponse(
            $this->getJsonFileContent('error-response.json'),
            502
        );

        $response = ResponseFactory::create($psrResponse);

        $this->assertInstanceOf(ErrorResponse::class, $response);
    }
}
