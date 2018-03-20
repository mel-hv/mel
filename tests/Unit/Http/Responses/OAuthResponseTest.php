<?php

namespace MelTests\Unit\Http\Responses;

use Mel\Http\Responses\OAuthResponse;
use MelTests\TestCase;

class OAuthResponseTest extends TestCase
{
    /**
     * @var string
     */
    protected $bodyJson;

    /**
     * @var \Psr\Http\Message\ResponseInterface
     */
    protected $psrResponse;

    /**
     * @var oAuthResponse
     */
    protected $oAuthResponse;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->bodyJson = $this->getJsonFileContent('oauth-response.json');

        $this->psrResponse = $this->createResponse($this->bodyJson);

        $this->oAuthResponse = new OAuthResponse($this->psrResponse);
    }

    public function testShouldReturnResponseBodyValues()
    {
        $responseBody = json_decode($this->bodyJson);

        $this->assertEquals($responseBody->access_token, $this->oAuthResponse->accessToken());
        $this->assertEquals($responseBody->token_type, $this->oAuthResponse->tokenType());
        $this->assertEquals($responseBody->expires_in, $this->oAuthResponse->expiresIn());
        $this->assertEquals($responseBody->refresh_token, $this->oAuthResponse->refreshToken());
        $this->assertEquals(explode(' ', $responseBody->scope), $this->oAuthResponse->scope());
    }
}
