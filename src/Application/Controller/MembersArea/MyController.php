<?php

namespace Application\Controller\MembersArea;

use Application\Entity\UserActionEntity;
use Application\Form\Type\User\SettingsType;
use Application\Form\Type\User\PasswordType;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class MyController
{
    /**
     * @param Application $app
     *
     * @return Response
     */
    public function indexAction(Application $app)
    {
        return $app->redirect(
            $app['url_generator']->generate('members-area.my.profile')
        );
    }

    /**
     * @param Application $app
     *
     * @return Response
     */
    public function profileAction(Application $app)
    {
        return new Response(
            $app['twig']->render(
                'contents/members-area/my/profile.html.twig'
            )
        );
    }

    /**
     * @param Request     $request
     * @param Application $app
     *
     * @return Response
     */
    public function settingsAction(Request $request, Application $app)
    {
        $userOld = $app['user']->toArray(false);

        $form = $app['form.factory']->create(
            new SettingsType(),
            $app['user']
        );

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $userEntity = $form->getData();

                if ($userEntity->getProfile()->getRemoveImage()) {
                    $userEntity->getProfile()->setImageUrl(null);
                }

                /*** Image ***/
                $userEntity
                    ->getProfile()
                    ->setImageUploadPath($app['baseUrl'].'/assets/uploads/')
                    ->setImageUploadDir(WEB_DIR.'/assets/uploads/')
                    ->imageUpload()
                ;
                $app['orm.em']->persist($userEntity);

                $userActionEntity = new UserActionEntity();
                $userActionEntity
                    ->setUser($userEntity)
                    ->setKey('user.settings.change')
                    ->setMessage('User has changed his settings!')
                    ->setData(array(
                        'old' => $userOld,
                        'new' => $app['user']->toArray(false),
                    ))
                    ->setIp($app['request']->getClientIp())
                    ->setUserAgent($app['request']->headers->get('User-Agent'))
                ;
                $app['orm.em']->persist($userActionEntity);

                $app['orm.em']->flush();

                $app['flashbag']->add(
                    'success',
                    $app['translator']->trans(
                        'Your settings were successfully saved!'
                    )
                );
            } else {
                $app['orm.em']->refresh($app['user']);
            }
        }

        return new Response(
            $app['twig']->render(
                'contents/members-area/my/settings.html.twig',
                array(
                    'form' => $form->createView(),
                )
            )
        );
    }

    /**
     * @param Request     $request
     * @param Application $app
     *
     * @return Response
     */
    public function passwordAction(Request $request, Application $app)
    {
        $form = $app['form.factory']->create(
            new PasswordType(),
            $app['user']
        );

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $userEntity = $form->getData();

                if ($userEntity->getPlainPassword()) {
                    $userEntity->setPlainPassword(
                        $userEntity->getPlainPassword(),
                        $app['security.encoder_factory']
                    );

                    $app['orm.em']->persist($userEntity);

                    $userActionEntity = new UserActionEntity();
                    $userActionEntity
                        ->setUser($userEntity)
                        ->setKey('user.password.change')
                        ->setMessage('User has changed his password!')
                        ->setIp($app['request']->getClientIp())
                        ->setUserAgent($app['request']->headers->get('User-Agent'))
                    ;
                    $app['orm.em']->persist($userActionEntity);

                    $app['orm.em']->flush();

                    $app['flashbag']->add(
                        'success',
                        $app['translator']->trans(
                            'Your password was successfully changed!'
                        )
                    );
                }
            }
        }

        return new Response(
            $app['twig']->render(
                'contents/members-area/my/password.html.twig',
                array(
                    'form' => $form->createView(),
                )
            )
        );
    }

    /**
     * @param Request     $request
     * @param Application $app
     *
     * @return Response
     */
    public function actionsAction(Request $request, Application $app)
    {
        $limitPerPage = $request->query->get('limit_per_page', 20);
        $currentPage = $request->query->get('page');

        $userActionResults = $app['orm.em']
            ->createQueryBuilder()
            ->select('ua')
            ->from('Application\Entity\UserActionEntity', 'ua')
            ->where('ua.user = ?1')
            ->setParameter(1, $app['user'])
        ;

        $pagination = $app['paginator']->paginate(
            $userActionResults,
            $currentPage,
            $limitPerPage,
            array(
                'route' => 'members-area.my.actions',
                'defaultSortFieldName' => 'ua.timeCreated',
                'defaultSortDirection' => 'desc',
                'searchFields' => array(
                    'ua.key',
                    'ua.ip',
                ),
            )
        );

        return new Response(
            $app['twig']->render(
                'contents/members-area/my/actions.html.twig',
                array(
                    'pagination' => $pagination,
                )
            )
        );
    }
}
