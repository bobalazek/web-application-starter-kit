<?php

namespace Application\Controller\MembersArea;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class StatisticsController
{
    /**
     * @param Application $app
     *
     * @return Response
     */
    public function indexAction(Application $app)
    {
        if (!$app['security']->isGranted('ROLE_ADMIN')) {
            $app->abort(403);
        }

        return new Response(
            $app['twig']->render(
                'contents/members-area/statistics/index.html.twig'
            )
        );
    }
}
