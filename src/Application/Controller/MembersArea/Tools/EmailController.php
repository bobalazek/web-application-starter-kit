<?php

namespace Application\Controller\MembersArea\Tools;

use Application\Tool\Helpers;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Borut BalaÅ¾ek <bobalazek124@gmail.com>
 */
class EmailController
{
    /**
     * @param Application $app
     *
     * @return Response
     */
    public function indexAction(Application $app)
    {
        $data = array();

        if (!$app['security']->isGranted('ROLE_ADMIN')) {
            $app->abort(403);
        }

        return new Response(
            $app['twig']->render(
                'contents/members-area/tools/email.html.twig',
                $data
            )
        );
    }

    /**
     * @param Request     $request
     * @param Application $app
     *
     * @return Response
     */
    public function previewTemplatesAction(Request $request, Application $app)
    {
        if (!$app['security']->isGranted('ROLE_ADMIN')) {
            $app->abort(403);
        }

        $data = array();

        $templates = array();
        $template = $request->query->get('template', false);
        $raw = $request->query->has('raw');

        // Set some possible global defaults for the template
        $emailData = array(
            'app' => $app,
            'user' => $app['user'],
            'content' => 'Hello world!',
            'formData' => array(
                'message' => 'Just a test message!',
            ),
        );

        if ($template && $raw) {
            $app['debug'] = false;
            $app['showProfiler'] = false;

            return $app['mailer.css_to_inline_styles_converter'](
                'emails/'.$template.'.html.twig',
                $emailData
            );
        }

        $templatesArray = Helpers::rglob(
            APP_DIR.'/templates/emails/*.html.twig'
        );

        if (!empty($templatesArray)) {
            foreach ($templatesArray as $templatePath) {
                $templatePath = str_replace(
                    APP_DIR.'/templates/emails/',
                    '',
                    $templatePath
                );

                $templates[] = str_replace(
                    '.html.twig',
                    '',
                    $templatePath
                );
            }
        }

        $data['template'] = $template;
        $data['templates'] = $templates;

        return new Response(
            $app['twig']->render(
                'contents/members-area/tools/email/preview-templates.html.twig',
                $data
            )
        );
    }
}
