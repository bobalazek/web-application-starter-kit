<?php

namespace Application\ControllerProvider\MembersArea;

use Silex\Application;
use Silex\ControllerProviderInterface;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class MyControllerProvider implements ControllerProviderInterface
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
            '',
            'Application\Controller\MembersArea\MyController::indexAction'
        )
        ->bind('members-area.my');

        $controllers->match(
            '/profile',
            'Application\Controller\MembersArea\MyController::profileAction'
        )
        ->bind('members-area.my.profile');

        $controllers->match(
            '/settings',
            'Application\Controller\MembersArea\MyController::settingsAction'
        )
        ->bind('members-area.my.settings');

        $controllers->match(
            '/password',
            'Application\Controller\MembersArea\MyController::passwordAction'
        )
        ->bind('members-area.my.password');

        $controllers->match(
            '/actions',
            'Application\Controller\MembersArea\MyController::actionsAction'
        )
        ->bind('members-area.my.actions');

        return $controllers;
    }
}
