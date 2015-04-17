<?php

namespace Application\ControllerProvider\MembersArea;

use Silex\Application;
use Silex\ControllerProviderInterface;

class MyControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->match(
            '',
            'Application\Controller\MembersArea\MyController::indexAction'
        )
        ->bind('members-area.my');

        $controllers->match(
            '/messages',
            'Application\Controller\MembersArea\MyController::messagesAction'
        )
        ->bind('members-area.my.messages');

        $controllers->match(
            '/messages/new',
            'Application\Controller\MembersArea\MyController::messagesNewAction'
        )
        ->bind('members-area.my.messages.new');

        $controllers->match(
            '/messages/{id}/reply',
            'Application\Controller\MembersArea\MyController::messagesReplyAction'
        )
        ->bind('members-area.my.messages.reply');

        $controllers->match(
            '/actions',
            'Application\Controller\MembersArea\MyController::actionsAction'
        )
        ->bind('members-area.my.actions');

        $controllers->match(
            '/notifications',
            'Application\Controller\MembersArea\MyController::notificationsAction'
        )
        ->bind('members-area.my.notifications');

        $controllers->match(
            '/notifications/acknowledge/{id}',
            'Application\Controller\MembersArea\MyController::notificationsAcknowledgeAction'
        )
        ->bind('members-area.my.notifications.acknowledge');

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
