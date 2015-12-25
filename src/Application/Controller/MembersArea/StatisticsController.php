<?php

namespace Application\Controller\MembersArea;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class StatisticsController
{
    public function indexAction(Request $request, Application $app)
    {
        return new Response(
            $app['twig']->render(
                'contents/members-area/statistics/index.html.twig'
            )
        );
    }
}
