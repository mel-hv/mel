<?php

namespace Mel\Http;

class OAuthResponse extends Response
{
    /**
     * Access key to private resources
     *
     * @return string|null
     */
    public function accessToken()
    {
        return $this->getBodyItem('access_token');
    }

    /**
     * Token type
     *
     * @return string|null
     */
    public function tokenType()
    {
        return $this->getBodyItem('token_type');
    }

    /**
     * Access token service life in seconds
     *
     * @return int
     */
    public function expiresIn()
    {
        return (int)$this->getBodyItem('expires_in', 0);
    }

    /**
     * The refresh token from the approval step
     *
     * @return string|null
     */
    public function refreshToken()
    {
        return $this->getBodyItem('refresh_token');
    }

    /**
     * Permissions given to the application
     *
     * @return array|null
     */
    public function scope()
    {
        $scope = $this->getBodyItem('scope');

        if (is_string($scope)) {
            return explode(' ', $scope);
        }

        return $scope;
    }
}