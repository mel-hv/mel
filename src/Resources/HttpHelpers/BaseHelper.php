<?php

namespace Mel\Resources\HttpHelpers;

use Mel\Exceptions\MelException;

trait BaseHelper
{
    /**
     * Get a uri instance of the paths list
     *
     * @param string $id
     *
     * @return string Path value
     * @throws MelException
     */
    protected function getPath($id)
    {
        if (!array_key_exists($id, $this->paths)) {
            throw new MelException(
                sprintf('Register a uri related to %1$s', $id)
            );
        }

        return $this->paths[$id];
    }
}