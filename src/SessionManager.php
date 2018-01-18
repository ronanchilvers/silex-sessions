<?php

namespace Ronanchilvers\Silex\Sessions;

use Symfony\Component\HttpFoundation\Cookie;

/**
 * Manager responsible moving the session data into and out of a cookie
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
class SessionManager
{
    /**
     * @var Symfony\Component\HttpFoundation\Cookie
     */
    protected $cookie;

    /**
     * Set the cookie for this manager
     *
     * @param Symfony\Component\HttpFoundation\Cookie $cookie
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function setCookie(Cookie $cookie)
    {
        $this->cookie = $cookie;
    }

    /**
     * Get the cookie for this manager
     *
     * @return Symfony\Component\HttpFoundation\Cookie
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function getCookie()
    {
        return $this->cookie;
    }

    /**
     * Create a new session object
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function create()
    {
        $data = [];
        if ($cookie instanceof Cookie) {
            $data = unserialize($cookie->getValue());
        }
    }
}
