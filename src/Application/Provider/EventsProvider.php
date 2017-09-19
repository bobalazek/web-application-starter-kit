<?php

namespace Application\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\EventListenerProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Application\EventListener;

/**
 * @author Borut Balazek <bobalazek124@gmail.com>
 */
class EventsProvider implements ServiceProviderInterface, EventListenerProviderInterface
{
    public function register(Container $app)
    {
    }

    public function subscribe(Container $app, EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addSubscriber(
            new EventListener\AuthenticationListener($app)
        );

        $dispatcher->addSubscriber(
            new EventListener\SecurityListener($app)
        );
    }
}
