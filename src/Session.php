<?php

namespace Ronanchilvers\Silex\Sessions;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException;
use Defuse\Crypto\Key;
use Exception;
use Ronanchilvers\Silex\Sessions\Session;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Manager responsible moving the session data into and out of a cookie
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
class Session
{
    /**
     * @var string
     */
    protected $options = [];

    /**
     * @var Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @var Defuse\Crypto\Key
     */
    protected $key;

    /**
     * @var array
     */
    protected $data = null;

    /**
     * @var array
     */
    protected $flashes = null;

    /**
     * Class constructor
     *
     * @param array $options
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function __construct($options = [])
    {
        $this->options = array_merge([
                'cookie.name' => 'session.cookie',
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
     * Set the request for this session
     *
     * @param Symfony\Component\HttpFoundation\Request
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Add the session cookie to a response object
     *
     * @param Symfony\Component\HttpFoundation\Response $response
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function addCookieToResponse(Response $response)
    {
        $this->load();
        $data = serialize([
            'data' => $this->data,
            'flashes' => $this->flashes
        ]);
        $data = Crypto::encrypt(
            $data,
            $this->getKey()
        );
        $cookie = new Cookie(
            $this->getOption('cookie.name', 'session.cookie'),
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
     * Does the session have a given key?
     *
     * @return boolean
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function has($key)
    {
        $this->load();
        return isset($this->data[$key]);
    }

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
        $this->load();
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
        $this->load();
        $this->data[$key] = $value;
    }

    /**
     * Remove a key from the session
     *
     * @param string $key
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function remove($key)
    {
        if (isset($this->data[$key])) {
            unset($this->data[$key]);
        }
    }

    /**
     * Get the flash messages for a given type
     *
     * @param string $type
     * @return array
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function getFlashes($type)
    {
        $this->load();
        if (!isset($this->flashes[$type])) {
            return [];
        }
        $messages = $this->flashes[$type];
        unset($this->flashes[$type]);

        return $messages;
    }

    /**
     * Set the flash message for a given type
     *
     * @param  string $type
     * @param  string $message
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function addFlash($type, $message)
    {
        $this->load();
        if (!isset($this->flashes[$type])) {
            $this->flashes[$type] = [];
        }
        $this->flashes[$type][] = $message;
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
     * Load session data from the request
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected function load()
    {
        if (!is_null($this->data)) {
            return;
        }
        if (!$this->request instanceof Request) {
            return;
        }
        $cookieName = str_replace('.', '_', $this->getOption('cookie.name'));
        $data = $this->request->cookies->get(
            $cookieName,
            null
        );
        if (!is_null($data)) {
            try {
                $data = Crypto::decrypt(
                    $data,
                    $this->getKey()
                );
                $data = unserialize($data);
                if (!is_null($data)) {
                    $this->data = $data['data'];
                    $this->flashes = $data['flashes'];
                }
            } catch (WrongKeyOrModifiedCiphertextException $ex) {
                // Session is killed
            }
        }
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
