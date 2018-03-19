<?php

namespace Mel\Http\Responses;

class OAuthResponse extends Response
{
    /**
     * Access key to private resources
     *
     * @return string|null
     */
    public function accessToken()
    {
        return $this->get('access_token');
    }

    /**
     * Token type
     *
     * @return string|null
     */
    public function tokenType()
    {
        return $this->get('token_type');
    }

    /**
     * Access token service life in seconds
     *
     * @return int
     */
    public function expiresIn()
    {
        return (int)$this->get('expires_in', 0);
    }

    /**
     * The refresh token from the approval step
     *
     * @return string|null
     */
    public function refreshToken()
    {
        return $this->get('refresh_token');
    }

    /**
     * Permissions given to the application
     *
     * @return array|null
     */
    public function scope()
    {
        $scope = $this->get('scope');

        if (is_string($scope)) {
            return explode(' ', $scope);
        }

        return $scope;
    }
}