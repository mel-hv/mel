<?php

namespace Mel\Auth\Storage;

class SessionStorage implements StorageInterface
{
    /**
     * Session reference
     *
     * @var array
     */
    protected $session;

    /**
     * @var string
     */
    protected $sessionId;

    /**
     * @var string
     */
    protected $prefix = 'mel.';

    public function __construct($debug = false)
    {
        if (session_status() != PHP_SESSION_ACTIVE && !headers_sent() && !$debug) {
            $sessionId = session_id();
            if (empty($sessionId)) {
                session_start();
                $sessionId = session_id();
            }
            $this->sessionId = $sessionId;
        }

        if (!is_null($this->getSessionId())) {
            $this->session = &$_SESSION;
        } else {
            $this->session = [];
        }
    }

    /**
     * Create item in actual session
     *
     * @param $name
     * @param $value
     */
    public function set($name, $value)
    {
        if (!empty($name)) {
            $fullName = $this->prefixedName($name);
            $this->remove($name);
            $this->session[$fullName] = $value;
        }
    }

    /**
     * Return if items exists in session
     *
     * @param $name
     *
     * @return bool
     */
    public function has($name)
    {
        return isset($this->session[$this->prefixedName($name)]);
    }

    /**
     * Return session item
     *
     * @param $name
     *
     * @return string|null
     */
    public function get($name)
    {
        if ($this->has($name)) {
            return $this->session[$this->prefixedName($name)];
        }

        return null;
    }

    /**
     * Remove item of the session
     *
     * @param $name
     */
    public function remove($name)
    {
        if ($this->has($name)) {
            unset($this->session[$this->prefixedName($name)]);
        }
    }

    /**
     * Get prefixed item key
     *
     * @param $name
     *
     * @return string
     */
    private function prefixedName($name)
    {
        return $this->prefix . $name;
    }

    /**
     * Get session id value
     *
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * Set prefix used to naming session items
     *
     * @param string $prefix
     */
    public function setPrefix($prefix)
    {
        if (!empty($prefix)) {
            $this->prefix = $prefix;
        }
    }
}