<?php

namespace Application\ControllerProvider\MembersArea;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;

/**
 * @author Borut Balazek <bobalazek124@gmail.com>
 */
class ToolsControllerProvider implements ControllerProviderInterface
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
            'Application\Controller\MembersArea\ToolsController::indexAction'
        )
        ->bind('members-area.tools');

        /***** Email *****/
        $controllers->match(
            '/email',
            'Application\Controller\MembersArea\Tools\EmailController::indexAction'
        )
        ->bind('members-area.tools.email');

        /*** Preview Templates ***/
        $controllers->match(
            '/email/preview-templates',
            'Application\Controller\MembersArea\Tools\EmailController::previewTemplatesAction'
        )
        ->bind('members-area.tools.email.preview-templates');

        /***** Database backups *****/
        $controllers->match(
            '/database-backup',
            'Application\Controller\MembersArea\ToolsController::databaseBackupAction'
        )
        ->bind('members-area.tools.database-backup');

        return $controllers;
    }
}
