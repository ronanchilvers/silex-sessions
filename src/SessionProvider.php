<?php

namespace Ronanchilvers\Silex\Sessions;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Ronanchilvers\Silex\Sessions\Console\Command\GenerateKeyCommand;
use Ronanchilvers\Silex\Sessions\SessionManager;
use Silex\Api\BootableProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
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
        $pimple['session.options'] = [];

        $pimple['session'] = function ($c) {
            return new Session(
                $c['session.options']
            );
        };

        // $pimple['session'] = function ($c) {
        //     return $c['session.manager']->getSession();
        // };

        if (isset($pimple['console'])) {
            $pimple->extend('console', function ($console, $c) {
                $console->add(new GenerateKeyCommand());

                return $console;
            });
        }
    }

    /**
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function boot(Application $app)
    {

        $app->before(function (Request $request) use ($app) {
            $app['session']->setRequest($request);
        }, Application::EARLY_EVENT);

        $app->after(function (Request $request, Response $response) use ($app) {
            $app['session']->addCookieToResponse($response);
        }, Application::LATE_EVENT);
    }
}
