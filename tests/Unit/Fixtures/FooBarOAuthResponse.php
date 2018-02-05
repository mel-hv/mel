<?php

namespace MelTests\Unit\Fixtures;

use GuzzleHttp\Psr7\Response;

class FooBarOAuthResponse extends Response
{
    const BODY_ARRAY_FORMAT = [
        "access_token"  => "APP_USR-6092-3246532-cb45c82853f6e620bb0deda096b128d3-8035443",
        "token_type"    => "bearer",
        "expires_in"    => 10800,
        "refresh_token" => "TG-5005b6b3e4b07e60756a3353",
        "scope"         => "write read offline_access",
    ];

    /**
     * FooResponse constructor.
     */
    public function __construct()
    {
        $body = json_encode(self::BODY_ARRAY_FORMAT);

        parent::__construct(
            202,
            [],
            $body,
            '1.1',
            null
        );
    }
}