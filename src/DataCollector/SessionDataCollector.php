<?php

namespace Ronanchilvers\Silex\Sessions\DataCollector;

use Ronanchilvers\Silex\Sessions\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * Data collector for session data
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
class SessionDataCollector extends DataCollector
{
    /**
     * @var Ronanchilvers\Silex\Sessions\Session
     */
    protected $session;

    /**
     * Class constructor
     *
     * @param Ronanchilvers\Silex\Sessions\Session $session
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $data = [
            'options' => $this->session->getOptions(),
            'data' => $this->session->peek(),
            'flashes' => $this->session->peekFlashes(),
        ];
        $this->data = $data;
    }

    /**
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function getName()
    {
        return 'session';
    }

    /**
     * Get the key count for the session
     *
     * @return int
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function getKeyCount()
    {
        return count($this->data['data']);
    }

    /**
     * Get the flash count for the session
     *
     * @return int
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function getFlashCount()
    {
        return count($this->data['flashes']);
    }

    /**
     * Get the session data
     *
     * @return array
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function getData()
    {
        return $this->data['data'];
    }

    /**
     * Get the session flashes
     *
     * @return array
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function getFlashes()
    {
        return $this->data['flashes'];
    }
}
