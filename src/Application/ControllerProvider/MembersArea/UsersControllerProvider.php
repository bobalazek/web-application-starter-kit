<?php

namespace Application\ControllerProvider\MembersArea;

use Silex\Application;
use Silex\ControllerProviderInterface;

class UsersControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->match(
            '',
            'Application\Controller\MembersArea\UsersController::indexAction'
        )
        ->bind('members-area.users');

        $controllers->match(
            '/new',
            'Application\Controller\MembersArea\UsersController::newAction'
        )
        ->bind('members-area.users.new');

        $controllers->match(
            '/{id}',
            'Application\Controller\MembersArea\UsersController::detailAction'
        )
        ->bind('members-area.users.detail');

        $controllers->match(
            '/{id}/edit',
            'Application\Controller\MembersArea\UsersController::editAction'
        )
        ->bind('members-area.users.edit');

        $controllers->match(
            '/{id}/remove',
            'Application\Controller\MembersArea\UsersController::removeAction'
        )
        ->bind('members-area.users.remove');

        return $controllers;
    }
}
