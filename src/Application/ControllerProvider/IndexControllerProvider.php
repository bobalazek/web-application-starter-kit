<?php

namespace Application\ControllerProvider;

use Silex\Application;
use Silex\ControllerProviderInterface;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class IndexControllerProvider
    implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->match(
            '/',
            'Application\Controller\IndexController::indexAction'
        )
        ->bind('index');

        return $controllers;
    }
}
