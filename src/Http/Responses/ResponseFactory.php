<?php

namespace Mel\Http\Responses;

use Psr\Http\Message\ResponseInterface as PsrResponse;
use Psr\Http\Message\RequestInterface as PsrRequest;

class ResponseFactory
{
    /**
     * Create a instance from the MelResponse using a psr Response implementation
     *
     * @param PsrResponse     $psrResponse
     * @param PsrRequest|null $psrRequest
     *
     * @return ErrorResponse|OAuthResponse|Response
     */
    public static function create(PsrResponse $psrResponse, PsrRequest $psrRequest = null)
    {
        $body = json_decode($psrResponse->getBody()->__toString(), true);

        if (self::hasAttributes(['error'], $body)) {
            return new ErrorResponse($psrResponse, $psrRequest);
        }

        if (self::hasAttributes(['access_token', 'token_type', 'expires_in'], $body)) {
            return new OAuthResponse($psrResponse, $psrRequest);
        }

        return new Response($psrResponse, $psrRequest);
    }

    /**
     * Return if response body has attributes
     *
     * @param array $attributes
     * @param array $body
     *
     * @return bool
     */
    public static function hasAttributes(array $attributes, array $body)
    {
        return !array_diff_key(array_flip($attributes), $body);
    }
}