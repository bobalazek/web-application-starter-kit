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
            '/actions',
            'Application\Controller\MembersArea\UsersController::actionsAction'
        )
        ->bind('members-area.users.actions');

        /***** Notifications *****/
        $controllers->match(
            '/notifications',
            'Application\Controller\MembersArea\Users\NotificationsController::indexAction'
        )
        ->bind('members-area.users.notifications');

        $controllers->match(
            '/notifications/new',
            'Application\Controller\MembersArea\Users\NotificationsController::newAction'
        )
        ->bind('members-area.users.notifications.new');

        $controllers->match(
            '/notifications/{id}/edit',
            'Application\Controller\MembersArea\Users\NotificationsController::editAction'
        )
        ->bind('members-area.users.notifications.edit');

        $controllers->match(
            '/notifications/{id}/remove',
            'Application\Controller\MembersArea\Users\NotificationsController::removeAction'
        )
        ->bind('members-area.users.notifications.remove');

        /***** Messages *****/
        $controllers->match(
            '/messages',
            'Application\Controller\MembersArea\Users\MessagesController::indexAction'
        )
        ->bind('members-area.users.messages');

        $controllers->match(
            '/messages/new',
            'Application\Controller\MembersArea\Users\MessagesController::newAction'
        )
        ->bind('members-area.users.messages.new');

        $controllers->match(
            '/messages/{id}/edit',
            'Application\Controller\MembersArea\Users\MessagesController::editAction'
        )
        ->bind('members-area.users.messages.edit');

        $controllers->match(
            '/messages/{id}/remove',
            'Application\Controller\MembersArea\Users\MessagesController::removeAction'
        )
        ->bind('members-area.users.messages.remove');

        $controllers->match(
            '/oauth-services',
            'Application\Controller\MembersArea\UsersController::oauthServicesAction'
        )
        ->bind('members-area.users.oauth-services');

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

        $controllers->match(
            '/{id}/lock',
            'Application\Controller\MembersArea\UsersController::lockAction'
        )
        ->bind('members-area.users.lock');

        $controllers->match(
            '/{id}/unlock',
            'Application\Controller\MembersArea\UsersController::unlockAction'
        )
        ->bind('members-area.users.unlock');

        return $controllers;
    }
}
