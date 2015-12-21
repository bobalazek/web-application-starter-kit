<?php

namespace Application\Controller\MembersArea;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class SettingsController
{
    public function indexAction(Request $request, Application $app)
    {
        $data = array();

        return new Response(
            $app['twig']->render(
                'contents/members-area/settings/index.html.twig',
                $data
            )
        );
    }
}
