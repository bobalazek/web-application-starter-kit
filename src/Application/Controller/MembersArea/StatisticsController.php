<?php

namespace Application\Controller\MembersArea;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Borut Balazek <bobalazek124@gmail.com>
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
        if (!$app['security.authorization_checker']->isGranted('ROLE_ADMIN')) {
            $app->abort(403);
        }

        return new Response(
            $app['twig']->render(
                'contents/members-area/statistics/index.html.twig'
            )
        );
    }
}
