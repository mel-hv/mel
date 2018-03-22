<?php

namespace MelTests\Unit\Http\Responses;

use Mel\Http\Responses\ResponseFactory;
use Mel\Http\Responses\Response;
use Mel\Http\Responses\OAuthResponse;
use Mel\Http\Responses\ErrorResponse;
use MelTests\TestCase;

class ResponseFactoryTest extends TestCase
{
    public function testShouldCreateDefaultResponseUsingOriginalResponseAndRequest()
    {
        $psrResponse = $this->createResponse(
            '{"status":202,"message":"Simple Message"}'
        );

        $psrRequest = $this->createRequest('GET', '/');

        $response = ResponseFactory::create($psrResponse, $psrRequest);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame($psrResponse, $response->psrResponse());
        $this->assertSame($psrRequest, $response->psrRequest());
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
