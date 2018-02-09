<?php

namespace Mel\Auth;

use Mel\Auth\Storage\StorageInterface;

class AccessToken implements AccessTokenInterface
{
    const ACCESS_TOKEN = 'access_token';

    const REFRESH_TOKEN = 'refresh_token';

    const EXPIRES_IN = 'expires_in';

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * AccessToken constructor.
     *
     * @param StorageInterface $storage
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * {@inheritdoc}
     */
    public function setToken($token)
    {
        $this->storage->set(static::ACCESS_TOKEN, $token);
    }

    /**
     * {@inheritdoc}
     */
    public function getToken()
    {
        return $this->storage->get(static::ACCESS_TOKEN);
    }

    /**
     * {@inheritdoc}
     */
    public function setRefreshToken($refreshToken)
    {
        $this->storage->set(static::REFRESH_TOKEN, $refreshToken);
    }

    /**
     * {@inheritdoc}
     */
    public function getRefreshToken()
    {
        return $this->storage->get(static::REFRESH_TOKEN);
    }

    /**
     * {@inheritdoc}
     */
    public function setExpiresIn($expireIn)
    {
        $this->storage->set(static::EXPIRES_IN, time() + $expireIn);
    }

    /**
     * {@inheritdoc}
     */
    public function getExpiresIn()
    {
        return $this->storage->get(static::EXPIRES_IN);
    }

    /**
     * {@inheritdoc}
     */
    public function isExpired()
    {
        return !($this->storage->has(static::EXPIRES_IN) && $this->storage->get(static::EXPIRES_IN) >= time());
    }

    /**
     * {@inheritdoc}
     */
    public function isValid()
    {
        return (!is_null($this->getToken()) && !$this->isExpired());
    }
}