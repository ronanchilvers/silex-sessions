<?php

namespace Ronanchilvers\Silex\Sessions;

use Serializable;

/**
 * Simple session object backed by a cookie
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
class Session implements Serializable
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * Get a variable from the session
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function get($key, $default = null)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }

        return $default;
    }

    /**
     * Set a session variable
     *
     * @param string $key
     * @param mixed $value
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Serialize this object
     *
     * @return string
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function serialize()
    {
        return serialize($this->data);
    }

    /**
     * Unserialize this object
     *
     * @param string $serialized
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function unserialize($serialized)
    {
        $this->data = unserialize($serialized);
    }
}
