<?php

namespace Application\ControllerProvider\MembersArea;

use Silex\Application;
use Silex\ControllerProviderInterface;

class RolesControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->match(
            '',
            'Application\Controller\MembersArea\RolesController::indexAction'
        )
        ->bind('members-area.roles');

        $controllers->match(
            '/new',
            'Application\Controller\MembersArea\RolesController::newAction'
        )
        ->bind('members-area.roles.new');

        $controllers->match(
            '/{id}/edit',
            'Application\Controller\MembersArea\RolesController::editAction'
        )
        ->bind('members-area.roles.edit');

        $controllers->match(
            '/{id}/remove',
            'Application\Controller\MembersArea\RolesController::removeAction'
        )
        ->bind('members-area.roles.remove');

        return $controllers;
    }
}
