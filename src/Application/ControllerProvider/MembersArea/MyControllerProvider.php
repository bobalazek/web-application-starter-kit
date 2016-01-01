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
            '/profile/settings',
            'Application\Controller\MembersArea\MyController::profileSettingsAction'
        )
        ->bind('members-area.my.profile.settings');

        $controllers->match(
            '/profile/settings/password',
            'Application\Controller\MembersArea\MyController::profileSettingsPasswordAction'
        )
        ->bind('members-area.my.profile.settings.password');

        return $controllers;
    }
}
