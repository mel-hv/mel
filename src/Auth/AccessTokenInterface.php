<?php

namespace Mel\Auth;

interface AccessTokenInterface
{
    /**
     * Set token value
     *
     * @param string $token
     */
    public function setToken($token);

    /**
     * Get token value
     *
     * @return string|null
     */
    public function getToken();

    /**
     * Set refresh token
     *
     * @param string $refreshToken
     */
    public function setRefreshToken($refreshToken);

    /**
     * Get refresh token
     *
     * @return string
     */
    public function getRefreshToken();

    /**
     * Set value of the token service life in seconds
     *
     * @param int $expireIn
     */
    public function setExpiresIn($expireIn);

    /**
     * Get value of token service life in seconds
     *
     * @return int
     */
    public function getExpiresIn();

    /**
     * Access token is expired or no
     *
     * @return bool
     */
    public function isExpired();

    /**
     * Access token is valid
     *
     * @return bool
     */
    public function isValid();
}