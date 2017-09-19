<?php

namespace Application\ControllerProvider;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;

/**
 * @author Borut Balazek <bobalazek124@gmail.com>
 */
class ApiControllerProvider implements ControllerProviderInterface
{
    /**
     * @param Application $app
     *
     * @return \Silex\ControllerCollection
     */
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->match(
            '/',
            'Application\Controller\ApiController::indexAction'
        )
        ->bind('api');

        return $controllers;
    }
}
