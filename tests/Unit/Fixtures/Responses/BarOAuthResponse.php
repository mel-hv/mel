<?php

namespace MelTests\Unit\Fixtures\Responses;

class BarOAuthResponse extends FooBaseResponse
{
    const BODY_ARRAY_FORMAT = [
        "access_token"  => "APP_USR-6092-3246532-cb45c82853f6e620bb0deda096b128d3-8035443",
        "token_type"    => "bearer",
        "expires_in"    => 10800,
        "refresh_token" => "TG-5005b6b3e4b07e60756a3353",
        "scope"         => "write read offline_access",
    ];
}