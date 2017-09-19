<?php

namespace Application\ControllerProvider\MembersArea;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;

/**
 * @author Borut Balazek <bobalazek124@gmail.com>
 */
class StatisticsControllerProvider implements ControllerProviderInterface
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
            'Application\Controller\MembersArea\StatisticsController::indexAction'
        )
        ->bind('members-area.statistics');

        return $controllers;
    }
}
