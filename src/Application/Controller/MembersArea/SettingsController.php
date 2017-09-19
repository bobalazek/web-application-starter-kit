<?php

namespace Application\Controller\MembersArea;

use Application\Form\Type\SettingsType;
use Application\Entity\SettingEntity;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Borut Balazek <bobalazek124@gmail.com>
 */
class SettingsController
{
    /**
     * @param Request     $request
     * @param Application $app
     *
     * @return Response
     */
    public function indexAction(Request $request, Application $app)
    {
        if (!$app['security.authorization_checker']->isGranted('ROLE_ADMIN')) {
            $app->abort(403);
        }

        $form = $app['form.factory']->create(
            new SettingsType($app)
        );

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $data = $form->getData();

                if (!empty($data)) {
                    foreach ($data as $key => $value) {
                        $settingEntity = $app['orm.em']
                            ->getRepository('Application\Entity\SettingEntity')
                            ->findOneByKey($key)
                        ;

                        if ($settingEntity === null) {
                            $settingEntity = new SettingEntity();

                            $settingEntity
                                ->setKey($key)
                            ;
                        }

                        $settingEntity
                            ->setValue($value)
                        ;

                        $app['orm.em']->persist($settingEntity);
                    }

                    try {
                        $app['orm.em']->flush();

                        $app['flashbag']->add(
                            'success',
                            $app['translator']->trans(
                                'The settings were successfully saved!'
                            )
                        );
                    } catch (\Exception $e) {
                        $app['flashbag']->add(
                            'danger',
                            $e->getMessage()
                        );
                    }

                    return $app->redirect(
                        $app['url_generator']->generate(
                            'members-area.settings'
                        )
                    );
                } else {
                    $app['flashbag']->add(
                        'info',
                        $app['translator']->trans(
                            'No changes were saved!'
                        )
                    );
                }
            }
        }

        return new Response(
            $app['twig']->render(
                'contents/members-area/settings/index.html.twig',
                [
                    'form' => $form->createView(),
                ]
            )
        );
    }
}
