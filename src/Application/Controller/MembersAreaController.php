<?php

namespace Application\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Application\Form\Type\User\RegisterType;
use Application\Form\Type\User\ResetPasswordType;
use Application\Entity\UserEntity;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class MembersAreaController
{
    /**
     * @param Application $app
     *
     * @return Response
     */
    public function indexAction(Application $app)
    {
        return new Response(
            $app['twig']->render(
                'contents/members-area/index.html.twig'
            )
        );
    }

    /**
     * @param Application $app
     *
     * @return Response
     */
    public function loginAction(Application $app)
    {
        if ($app['security.authorization_checker']->isGranted('ROLE_USER')) {
            return $app->redirect(
                $app['url_generator']->generate('members-area')
            );
        }

        $data = array(
            'lastUsername' => $app['session']->get('_security.last_username'),
            'lastError' => $app['security.last_error']($app['request']),
            'csrfToken' => $app['form.csrf_provider']->getToken('authenticate'),
        );

        return new Response(
            $app['twig']->render(
                'contents/members-area/login.html.twig',
                $data
            )
        );
    }

    /**
     * @param Application $app
     *
     * @return Response
     */
    public function logoutAction(Application $app)
    {
        return new Response(
            $app['twig']->render(
                'contents/members-area/logout.html.twig'
            )
        );
    }

    /**
     * @param Request     $request
     * @param Application $app
     *
     * @return Response
     */
    public function registerAction(Request $request, Application $app)
    {
        if ($app['security.authorization_checker']->isGranted('ROLE_USER')) {
            return $app->redirect(
                $app['url_generator']->generate('members-area')
            );
        }

        $data = array();

        $code = $request->query->has('code')
            ? $request->query->get('code')
            : false
        ;
        $action = $code
            ? 'confirm'
            : 'register'
        ;
        $alert = false;
        $alertMessage = '';

        $form = $app['form.factory']->create(
            new RegisterType(),
            new UserEntity()
        );

        if ($action == 'confirm') {
            $userEntity = $app['orm.em']
                ->getRepository('Application\Entity\UserEntity')
                ->findOneByActivationCode($code)
            ;

            if ($userEntity) {
                $userEntity
                    ->setActivationCode(null)
                    ->enable()
                ;

                $app['orm.em']->merge($userEntity);
                $app['orm.em']->flush();

                $app['application.mailer']
                    ->swiftMessageInitializeAndSend(array(
                        'subject' => $app['name'].' - '.$app['translator']->trans('Welcome'),
                        'to' => array($userEntity->getEmail()),
                        'body' => 'emails/users/register-welcome.html.twig',
                        'type' => 'user.register.welcome',
                        'templateData' => array(
                            'user' => $userEntity,
                        ),
                    ))
                ;

                $alert = 'success';
                $alertMessage = 'Your account has been activated!';
            } else {
                $alert = 'danger';
                $alertMessage = 'This activation code was not found!';
            }
        } else {
            if (
                $request->getMethod() == 'POST' &&
                $app['userSystemOptions']['registrationEnabled']
            ) {
                $form->handleRequest($request);

                if ($form->isValid()) {
                    $userEntity = $form->getData();

                    $userEntity->setPlainPassword(
                        $userEntity->getPlainPassword(),
                        $app['security.encoder_factory']
                    );

                    $app['application.mailer']
                        ->swiftMessageInitializeAndSend(array(
                            'subject' => $app['name'].' - '.$app['translator']->trans('Registration'),
                            'to' => array($userEntity->getEmail()),
                            'body' => 'emails/users/register.html.twig',
                            'type' => 'user.register',
                            'templateData' => array(
                                'user' => $userEntity,
                            ),
                        ))
                    ;

                    $app['orm.em']->persist($userEntity);
                    $app['orm.em']->flush();

                    $alert = 'success';
                    $alertMessage = 'You have successfully registered. We have sent you an confirmation email. Please click the link inside to activate your account.';
                }
            }
        }

        $data['form'] = $form->createView();
        $data['alert'] = $alert;
        $data['alertMessage'] = $alertMessage;

        return new Response(
            $app['twig']->render(
                'contents/members-area/register.html.twig',
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
    public function resetPasswordAction(Request $request, Application $app)
    {
        if ($app['security.authorization_checker']->isGranted('ROLE_USER')) {
            return $app->redirect(
                $app['url_generator']->generate('members-area')
            );
        }

        $data = array();

        $code = $request->query->has('code')
            ? $request->query->get('code')
            : false
        ;
        $action = $code
            ? 'reset'
            : 'request'
        ;
        $alert = false;
        $alertMessage = '';

        $form = $app['form.factory']->create(
            new ResetPasswordType($action),
            new UserEntity()
        );

        if ($action == 'reset') {
            $userEntity = $app['orm.em']
                ->getRepository('Application\Entity\UserEntity')
                ->findOneByResetPasswordCode($code)
            ;

            if ($userEntity) {
                if ($request->getMethod() == 'POST') {
                    $form->handleRequest($request);

                    if ($form->isValid()) {
                        $temporaryUserEntity = $form->getData();

                        $userEntity
                            ->setResetPasswordCode(null)
                            ->setPlainPassword(
                                $temporaryUserEntity->getPlainPassword(),
                                $app['security.encoder_factory']
                            )
                        ;

                        $app['orm.em']->persist($userEntity);
                        $app['orm.em']->flush();

                        $app['application.mailer']
                            ->swiftMessageInitializeAndSend(array(
                                'subject' => $app['name'].' - '.$app['translator']->trans('Reset Password Confirmation'),
                                'to' => array(
                                    $userEntity->getEmail() => $userEntity->getProfile()->getFullName(),
                                ),
                                'body' => 'emails/users/reset-password-confirmation.html.twig',
                                'templateData' => array(
                                    'user' => $userEntity,
                                ),
                            ))
                        ;

                        $alert = 'success';
                        $alertMessage = 'You password has been reset successfully.';
                    }
                }
            } else {
                $alert = 'danger';
                $alertMessage = 'This reset code was not found.';
            }
        } else {
            if ($request->getMethod() == 'POST') {
                $form->handleRequest($request);

                if ($form->isValid()) {
                    $temporaryUserEntity = $form->getData();

                    $userEntity = $app['orm.em']
                        ->getRepository('Application\Entity\UserEntity')
                        ->findOneByEmail(
                            $temporaryUserEntity->getEmail()
                        );

                    if ($userEntity) {
                        $app['application.mailer']
                            ->swiftMessageInitializeAndSend(array(
                                'subject' => $app['name'].' - '.$app['translator']->trans('Reset password'),
                                'to' => array($userEntity->getEmail()),
                                'body' => 'emails/users/reset-password.html.twig',
                                'type' => 'user.reset_password',
                                'templateData' => array(
                                    'user' => $userEntity,
                                ),
                            ))
                        ;

                        $alert = 'success';
                        $alertMessage = 'We have sent you an email. The link inside the email will lead you to a reset page.';
                    } else {
                        $alert = 'danger';
                        $alertMessage = 'This email was not found in our database.';
                    }
                }
            }
        }

        $data['code'] = $code;
        $data['action'] = $action;
        $data['form'] = $form->createView();
        $data['alert'] = $alert;
        $data['alertMessage'] = $alertMessage;

        return new Response(
            $app['twig']->render(
                'contents/members-area/reset-password.html.twig',
                $data
            )
        );
    }
}
