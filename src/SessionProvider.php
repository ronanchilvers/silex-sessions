<?php

namespace Ronanchilvers\Silex\Sessions;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Ronanchilvers\Silex\Sessions\SessionManager;
use Silex\Api\BootableProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response;

/**
 * Provider for sessions
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
class SessionProvider implements
    ServiceProviderInterface,
    BootableProviderInterface
{
    /**
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function register(Container $pimple)
    {
        $pimple['session.manager'] = function () {
            return new SessionManager();
        };

        $pimple['session'] = function ($c) {
            return $c['session.manager']->create();
        };
    }

    /**
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function boot(Application $app)
    {
        $app->before(function (Request $request) use ($app) {
            $manager = $app['session.manager'];
            // @TODO Remove var_dump
            var_dump('before'); exit();
        }, Application::EARLY_EVENT);

        $app->after(function (Request $request, Response $response) use ($app) {
            $manager = $app['session.manager'];
            // @TODO Remove var_dump
            var_dump('here'); exit();
        }, Application::EARLY_EVENT);
    }
}
