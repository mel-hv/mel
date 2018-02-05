<?php

namespace MelTests\Unit\Auth;

use Mockery;
use PHPUnit\Framework\TestCase;
use Mel\Auth\AccessToken;
use Mel\Auth\Storage\StorageInterface;

class AccessTokenTest extends TestCase
{
    protected function tearDown()
    {
        parent::tearDown();
        Mockery::close();
    }


    public function testShouldSetToken()
    {
        $token = 'token-value';

        $storage = Mockery::mock(StorageInterface::class);

        $storage->shouldReceive('set')
            ->once()
            ->with('access_token', $token);

        $storage->shouldReceive('get')
            ->once()
            ->with('access_token')
            ->andReturn($token);


        $accessToken = new AccessToken($storage);
        $accessToken->setToken($token);

        $this->assertEquals($token, $accessToken->getToken());
    }

    public function testShouldSetRefreshToken()
    {
        $refreshToken = 'refresh-token-value';

        $storage = Mockery::mock(StorageInterface::class);

        $storage->shouldReceive('set')
            ->once()
            ->with('refresh_token', $refreshToken);

        $storage->shouldReceive('get')
            ->once()
            ->with('refresh_token')
            ->andReturn($refreshToken);


        $accessToken = new AccessToken($storage);
        $accessToken->setRefreshToken($refreshToken);

        $this->assertEquals($refreshToken, $accessToken->getRefreshToken());
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
