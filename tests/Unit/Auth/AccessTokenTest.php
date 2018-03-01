<?php

namespace MelTests\Unit\Auth;

use Mel\Auth\AccessToken;
use Mel\Auth\Storage\StorageInterface;
use MelTests\TestCase;
use Mockery;

class AccessTokenTest extends TestCase
{
    public function testShouldSetToken()
    {
        $storage = Mockery::mock(StorageInterface::class);

        $storage->shouldReceive('set')
            ->once()
            ->with('access_token', $this->accessToken);

        $storage->shouldReceive('get')
            ->once()
            ->with('access_token')
            ->andReturn($this->accessToken);


        $accessToken = new AccessToken($storage);
        $accessToken->setToken($this->accessToken);

        $this->assertEquals($this->accessToken, $accessToken->getToken());
    }

    public function testShouldSetRefreshToken()
    {
        $storage = Mockery::mock(StorageInterface::class);

        $storage->shouldReceive('set')
            ->once()
            ->with('refresh_token', $this->refreshToken);

        $storage->shouldReceive('get')
            ->once()
            ->with('refresh_token')
            ->andReturn($this->refreshToken);


        $accessToken = new AccessToken($storage);
        $accessToken->setRefreshToken($this->refreshToken);

        $this->assertEquals($this->refreshToken, $accessToken->getRefreshToken());
    }

    public function testShouldSaveTimeToExpire()
    {
        $now = time();
        $expiresIn = 42;
        $expected = $now + $expiresIn;

        $storage = Mockery::mock(StorageInterface::class);

        $storage->shouldReceive('set')
            ->once()
            ->with('expires_in', $expected);

        $storage->shouldReceive('get')
            ->once()
            ->with('expires_in')
            ->andReturn($expected);

        $accessToken = new AccessToken($storage);
        $accessToken->setExpiresIn(42);

        $this->assertEquals($expected, $accessToken->getExpiresIn());
    }

    public function testReturnTrueIfTokenIsExpired()
    {
        $storage = Mockery::mock(StorageInterface::class);

        $expiresIn = time() - 42;

        $storage->shouldReceive('get')
            ->once()
            ->with('expires_in')
            ->andReturn($expiresIn);

        $storage->shouldReceive('has')
            ->once()
            ->with('expires_in')
            ->andReturn(true);


        $accessToken = new AccessToken($storage);

        $this->assertTrue($accessToken->isExpired());
    }

    public function testReturnTrueIfTokenIsValid()
    {
        $storage = Mockery::mock(StorageInterface::class);

        $expiresIn = time() + 3600;

        $storage->shouldReceive('get')
            ->once()
            ->with('access_token')
            ->andReturn('token-value');

        $storage->shouldReceive('get')
            ->once()
            ->with('expires_in')
            ->andReturn($expiresIn);

        $storage->shouldReceive('has')
            ->once()
            ->with('expires_in')
            ->andReturn(true);

        $accessToken = new AccessToken($storage);

        $this->assertTrue($accessToken->isValid());
    }
}
