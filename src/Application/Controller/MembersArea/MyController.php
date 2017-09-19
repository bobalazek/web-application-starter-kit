<?php

namespace Application\Controller\MembersArea;

use Application\Entity\UserActionEntity;
use Application\Form\Type\User\SettingsType;
use Application\Form\Type\User\PasswordType;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Borut Balazek <bobalazek124@gmail.com>
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
        $userOld = clone $app['user'];
        $userOldArray = $userOld->toArray(false);

        $form = $app['form.factory']->create(
            SettingsType::class,
            $app['user']
        );
        $newEmailCode = $request->query->get('new_email_code');

        if ($newEmailCode) {
            $userByNewEmailCode = $app['orm.em']
                ->getRepository('Application\Entity\UserEntity')
                ->findOneByNewEmailCode($newEmailCode)
            ;

            if (
                $userByNewEmailCode &&
                $userByNewEmailCode === $app['user']
            ) {
                $app['user']
                    ->setNewEmailCode(null)
                    ->setEmail($app['user']->getNewEmail())
                    ->setNewEmail(null)
                ;
                $app['orm.em']->persist($app['user']);
                $app['orm.em']->flush();

                $app['application.mailer']
                    ->swiftMessageInitializeAndSend([
                        'subject' => $app['name'].' - '.$app['translator']->trans('Email change confirmation'),
                        'to' => [$app['user']->getEmail()],
                        'body' => 'emails/users/new-email-confirmation.html.twig',
                        'templateData' => [
                            'user' => $app['user'],
                        ],
                    ])
                ;

                $app['flashbag']->add(
                    'success',
                    $app['translator']->trans(
                        'You have successfully changed to your new email address!'
                    )
                );
            } else {
                $app['flashbag']->add(
                    'warning',
                    $app['translator']->trans(
                        'The new email code is invalid. Please change your new email again!'
                    )
                );
            }

            return $app->redirect(
                $app['url_generator']->generate('members-area.my.settings')
            );
        }

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

                if ($userOld->getEmail() !== $userEntity->getEmail()) {
                    $userEntity
                        ->setNewEmailCode(md5(uniqid(null, true)))
                        ->setNewEmail($userEntity->getEmail())
                        ->setEmail($userOld->getEmail())
                    ;

                    $app['application.mailer']
                        ->swiftMessageInitializeAndSend([
                            'subject' => $app['name'].' - '.$app['translator']->trans('Email change'),
                            'to' => [$userEntity->getNewEmail()],
                            'body' => 'emails/users/new-email.html.twig',
                            'templateData' => [
                                'user' => $userEntity,
                            ],
                        ])
                    ;

                    $app['flashbag']->add(
                        'success',
                        $app['translator']->trans(
                            'Please confirm your new password, by clicking the confirmation link we just sent you to the new email address!'
                        )
                    );
                }

                $userActionEntity = new UserActionEntity();
                $userActionEntity
                    ->setUser($userEntity)
                    ->setKey('user.settings.change')
                    ->setMessage('User has changed his settings!')
                    ->setData([
                        'old' => $userOldArray,
                        'new' => $userEntity->toArray(false),
                    ])
                    ->setIp($request->getClientIp())
                    ->setUserAgent($request->headers->get('User-Agent'))
                ;

                $app['orm.em']->persist($userActionEntity);
                $app['orm.em']->flush();

                $app['flashbag']->add(
                    'success',
                    $app['translator']->trans(
                        'Your settings were successfully saved!'
                    )
                );

                return $app->redirect(
                    $app['url_generator']->generate('members-area.my.settings')
                );
            } else {
                $app['orm.em']->refresh($app['user']);
            }
        }

        return new Response(
            $app['twig']->render(
                'contents/members-area/my/settings.html.twig',
                [
                    'form' => $form->createView(),
                ]
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
            PasswordType::class,
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
                        ->setIp($request->getClientIp())
                        ->setUserAgent($request->headers->get('User-Agent'))
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
                [
                    'form' => $form->createView(),
                ]
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

        $pagination = $app['application.paginator']->paginate(
            $userActionResults,
            $currentPage,
            $limitPerPage,
            [
                'route' => 'members-area.my.actions',
                'defaultSortFieldName' => 'ua.timeCreated',
                'defaultSortDirection' => 'desc',
                'searchFields' => [
                    'ua.key',
                    'ua.ip',
                ],
            ]
        );

        return new Response(
            $app['twig']->render(
                'contents/members-area/my/actions.html.twig',
                [
                    'pagination' => $pagination,
                ]
            )
        );
    }
}
