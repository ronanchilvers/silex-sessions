<?php

namespace Ronanchilvers\Silex\Sessions;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException;
use Defuse\Crypto\Key;
use Ronanchilvers\Silex\Sessions\Session;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Manager responsible moving the session data into and out of a cookie
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
class SessionManager
{
    /**
     * @var string
     */
    protected $options = [];

    /**
     * @var Ronanchilvers\Silex\Sessions\Session
     */
    protected $session;

    /**
     * @var Defuse\Crypto\Key
     */
    protected $key;

    /**
     * Class constructor
     *
     * @param array $options
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function __construct($options = [])
    {
        $this->options = array_merge([
                'cookie.name' => 'silex.session.cookie',
                'cookie.expire' => 0,
                'cookie.path' => '/',
                'cookie.domain' => null,
                'cookie.secure' => false,
                'cookie.http.only' => true,
                'encryption.key' => false,
            ],
            $options
        );
    }

    /**
     * Set the cookie from a request object
     *
     * @param Symfony\Component\HttpFoundation\Request
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function setSessionFromRequest(Request $request)
    {
        $cookieName = str_replace('.', '_', $this->getOption('cookie.name'));
        $session = $request->cookies->get(
            $cookieName,
            null
        );
        if (!is_null($session)) {
            try {
                $session = Crypto::decrypt(
                    $session,
                    $this->getKey()
                );
                $session = unserialize($session);
                if ($session instanceof Session) {
                    $this->session = $session;
                }
            } catch (WrongKeyOrModifiedCiphertextException $ex) {
                // Session is killed
            }
        }
    }

    /**
     * Add the session cookie to a response object
     *
     * @param Symfony\Component\HttpFoundation\Response $response
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function addCookieToResponse(Response $response)
    {
        $data = serialize($this->getSession());
        $data = Crypto::encrypt(
            $data,
            $this->getKey()
        );
        $cookie = new Cookie(
            $this->getOption('cookie.name', 'silex.session.cookie'),
            $data,
            $this->getOption('cookie.expire', 0),
            $this->getOption('cookie.path', '/'),
            $this->getOption('cookie.domain', null),
            $this->getOption('cookie.secure', false),
            $this->getOption('cookie.http.only', true)
        );
        $response->headers->setCookie(
            $cookie
        );
    }

    /**
     * Create a new session object
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function getSession()
    {
        if (!$this->session instanceof Session) {
            $this->session = new Session();
        }

        return $this->session;
    }

    /**
     * Get an encryption key object instance
     *
     * @return Defuse\Crypto\Key
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected function getKey()
    {
        if (!$this->key instanceof Key) {
            if (false === ($key = $this->getOption('encryption.key', false))) {
                throw new Exception('Invalid key - have you configured one yet?');
            }
            $this->key = Key::loadFromAsciiSafeString($key);
        }

        return $this->key;
    }

    /**
     * Get an option from the options array
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected function getOption($key, $default = null)
    {
        if (isset($this->options[$key])) {
            return $this->options[$key];
        }

        return $default;
    }
}
