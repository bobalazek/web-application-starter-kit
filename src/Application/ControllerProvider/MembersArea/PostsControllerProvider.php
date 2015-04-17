<?php

namespace Application\ControllerProvider\MembersArea;

use Silex\Application;
use Silex\ControllerProviderInterface;

class PostsControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->match(
            '',
            'Application\Controller\MembersArea\PostsController::indexAction'
        )
        ->bind('members-area.posts');

        $controllers->match(
            '/new',
            'Application\Controller\MembersArea\PostsController::newAction'
        )
        ->bind('members-area.posts.new');

        $controllers->match(
            '/{id}/edit',
            'Application\Controller\MembersArea\PostsController::editAction'
        )
        ->bind('members-area.posts.edit');

        $controllers->match(
            '/{id}/remove',
            'Application\Controller\MembersArea\PostsController::removeAction'
        )
        ->bind('members-area.posts.remove');

        return $controllers;
    }
}
