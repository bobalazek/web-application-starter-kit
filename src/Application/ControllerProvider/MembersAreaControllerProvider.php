<?php

namespace Application\ControllerProvider;

use Silex\Application;
use Silex\ControllerProviderInterface;

class MembersAreaControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        /*
         * Members Area - Dashboard / Index
         */
        $controllers->match(
            '',
            'Application\Controller\MembersAreaController::indexAction'
        )
        ->bind('members-area');

        $controllers->match(
            '/login',
            'Application\Controller\MembersAreaController::loginAction'
        )
        ->bind('members-area.login');

        $controllers->match(
            '/logout',
            'Application\Controller\MembersAreaController::logoutAction'
        )
        ->bind('members-area.logout');

        $controllers->match(
            '/register',
            'Application\Controller\MembersAreaController::registerAction'
        )
        ->bind('members-area.register');

        $controllers->match(
            '/reset-password',
            'Application\Controller\MembersAreaController::resetPasswordAction'
        )
        ->bind('members-area.reset-password');

        return $controllers;
    }
}
