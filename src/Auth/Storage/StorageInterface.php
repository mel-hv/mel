<?php

namespace Mel\Auth\Storage;

interface StorageInterface
{
    /**
     * Return if item exists
     *
     * @param string $name
     *
     * @return bool
     */
    public function has($name);

    /**
     * Get specified item if exist.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function get($name);

    /**
     * Set item name and value
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return mixed
     */
    public function set($name, $value);

    /**
     * Remove item
     *
     * @param $name
     */
    public function remove($name);
}