<?php

namespace Ronanchilvers\Silex\Sessions;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Ronanchilvers\Silex\Sessions\DataCollector\SessionDataCollector;

/**
 * Provider for session data collection with the web profiler
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
class SessionWebProfilerProvider implements ServiceProviderInterface
{
    /**
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function register(Container $pimple)
    {
        $pimple['data_collectors'] = $pimple->extend('data_collectors', function($collectors, $pimple) {
            $collectors['session'] = function ($pimple) {
                return new SessionDataCollector(
                    $pimple['session']
                );
            };

            return $collectors;
        });
        $pimple['twig.loader.filesystem']->addPath(
            __DIR__ . '/../resources/views',
            'Session'
        );
        $pimple->extend('data_collector.templates', function ($templates) {
            $templates[] = ['session', '@Session/session.html.twig'];

            return $templates;
        });
    }
}
