<?php

namespace MelTests\Unit\Fixtures;

use GuzzleHttp\Psr7\Response;

class FooBarOAuthResponse extends Response
{
    const BODY_ARRAY_FORMAT = [
        "access_token" => "APP_USR-6092-3246532-cb45c82853f6e620bb0deda096b128d3-8035443",
        "token_type"   => "bearer",
        "expires_in"   => 10800,
        "scope"        => "write read",
    ];

    /**
     * FooResponse constructor.
     */
    public function __construct()
    {
        parent::__construct(
            202,
            [],
            '{"access_token":"APP_USR-6092-3246532-cb45c82853f6e620bb0deda096b128d3-8035443", "token_type":"bearer", "expires_in":10800, scope":"write read"}',
            '1.1',
            null
        );
    }
}