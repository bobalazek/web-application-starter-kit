<?php

namespace Application\Controller;

use Application\Entity\UserEntity;
use Application\Entity\UserActionEntity;
use Application\Form\Type\User\RegisterType;
use Application\Form\Type\User\ResetPasswordType;
use Application\Form\Type\User\ResetPasswordRequestType;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Borut Balazek <bobalazek124@gmail.com>
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
    public function loginAction(Request $request, Application $app)
    {
        if ($app['security.authorization_checker']->isGranted('ROLE_USER')) {
            return $app->redirect(
                $app['url_generator']->generate('members-area')
            );
        }

        $data = [
            'lastUsername' => $app['session']->get('_security.last_username'),
            'lastError' => $app['security.last_error']($request),
            'csrfToken' => $app['csrf.token_manager']->getToken('authenticate'),
        ];

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
            RegisterType::class,
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

                $app['orm.em']->persist($userEntity);
                $app['orm.em']->flush();

                $app['application.mailer']
                    ->swiftMessageInitializeAndSend([
                        'subject' => $app['name'].' - '.$app['translator']->trans('Welcome'),
                        'to' => [$userEntity->getEmail()],
                        'body' => 'emails/users/register-welcome.html.twig',
                        'templateData' => [
                            'user' => $userEntity,
                        ],
                    ])
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
                $app['user_system_options']['registrations_enabled']
            ) {
                $form->handleRequest($request);

                if ($form->isValid()) {
                    $userEntity = $form->getData();

                    $userEntity->setPlainPassword(
                        $userEntity->getPlainPassword(),
                        $app['security.encoder_factory']
                    );

                    $app['application.mailer']
                        ->swiftMessageInitializeAndSend([
                            'subject' => $app['name'].' - '.$app['translator']->trans('Registration'),
                            'to' => [$userEntity->getEmail()],
                            'body' => 'emails/users/register.html.twig',
                            'templateData' => [
                                'user' => $userEntity,
                            ],
                        ])
                    ;

                    $app['orm.em']->persist($userEntity);
                    $app['orm.em']->flush();

                    $alert = 'success';
                    $alertMessage = 'You have successfully registered. We have sent you an confirmation email. Please click the link inside to activate your account.';
                }
            }
        }

        return new Response(
            $app['twig']->render(
                'contents/members-area/register.html.twig',
                [
                    'form' => $form->createView(),
                    'alert' => $alert,
                    'alertMessage' => $alertMessage,
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
    public function resetPasswordAction(Request $request, Application $app)
    {
        if ($app['security.authorization_checker']->isGranted('ROLE_USER')) {
            return $app->redirect(
                $app['url_generator']->generate('members-area')
            );
        }

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

        $currentDateTime = new \DateTime();
        $form = $app['form.factory']->create(
            $action == 'reset'
                ? ResetPasswordType::class
                : ResetPasswordRequestType::class,
            new UserEntity()
        );

        if ($action == 'reset') {
            $userEntity = $app['orm.em']
                ->getRepository('Application\Entity\UserEntity')
                ->findOneByResetPasswordCode($code)
            ;

            if ($userEntity) {
                $isResetPasswordCodeExpired = $currentDateTime > $userEntity->getTimeResetPasswordCodeExpires();

                if ($isResetPasswordCodeExpired) {
                    $alert = 'danger';
                    $alertMessage = 'This code has expired. Please try to reset your password again.';
                } else {
                    if ($request->getMethod() == 'POST') {
                        $form->handleRequest($request);

                        if ($form->isValid()) {
                            $temporaryUserEntity = $form->getData();

                            $userEntity
                                ->setResetPasswordCode(null)
                                ->setTimeResetPasswordCodeExpires(null)
                                ->setPlainPassword(
                                    $temporaryUserEntity->getPlainPassword(),
                                    $app['security.encoder_factory']
                                )
                            ;
                            $app['orm.em']->persist($userEntity);

                            $userActionEntity = new UserActionEntity();
                            $userActionEntity
                                ->setUser($userEntity)
                                ->setKey('user.password.reset')
                                ->setMessage('User has reset his password!')
                                ->setIp($request->getClientIp())
                                ->setUserAgent($request->headers->get('User-Agent'))
                            ;
                            $app['orm.em']->persist($userActionEntity);

                            $app['orm.em']->flush();

                            $app['application.mailer']
                                ->swiftMessageInitializeAndSend([
                                    'subject' => $app['name'].' - '.$app['translator']->trans('Reset Password Confirmation'),
                                    'to' => [
                                        $userEntity->getEmail() => $userEntity->getProfile()->getFullName(),
                                    ],
                                    'body' => 'emails/users/reset-password-confirmation.html.twig',
                                    'templateData' => [
                                        'user' => $userEntity,
                                    ],
                                ])
                            ;

                            $alert = 'success';
                            $alertMessage = 'Your password has been reset successfully.';
                        }
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
                        )
                    ;

                    if ($userEntity) {
                        $isPasswordCodeAlreadySent = $currentDateTime < $userEntity->getTimeResetPasswordCodeExpires();

                        if ($isPasswordCodeAlreadySent) {
                            $alert = 'info';
                            $alertMessage = 'A reset password email was already sent to you. Please check your email address for further instructions.';
                        } else {
                            $userEntity
                                ->setResetPasswordCode(md5(uniqid(null, true)))
                                ->setTimeResetPasswordCodeExpires(
                                    new \Datetime(
                                        'now +'.$app['user_system_options']['reset_password_expiry_time']
                                    )
                                )
                            ;
                            $app['orm.em']->persist($userEntity);

                            $userActionEntity = new UserActionEntity();
                            $userActionEntity
                                ->setUser($userEntity)
                                ->setKey('user.password.request')
                                ->setMessage('User has requested a password reset!')
                                ->setIp($request->getClientIp())
                                ->setUserAgent($request->headers->get('User-Agent'))
                            ;
                            $app['orm.em']->persist($userActionEntity);

                            // In the REALLY unlikely case that the reset password code wouldn't be unique
                            try {
                                $app['orm.em']->flush();

                                $app['application.mailer']
                                    ->swiftMessageInitializeAndSend([
                                        'subject' => $app['name'].' - '.$app['translator']->trans('Reset password'),
                                        'to' => [$userEntity->getEmail()],
                                        'body' => 'emails/users/reset-password.html.twig',
                                        'templateData' => [
                                            'user' => $userEntity,
                                        ],
                                    ])
                                ;

                                $alert = 'success';
                                $alertMessage = 'We have sent you an email. The link inside the email will lead you to a reset page.';
                            } catch (\Exception $e) {
                                $alert = 'danger';
                                $alertMessage = 'Whops. Something went wrong. Please try again.';
                            }
                        }
                    } else {
                        $alert = 'danger';
                        $alertMessage = 'This email was not found in our database.';
                    }
                }
            }
        }

        return new Response(
            $app['twig']->render(
                'contents/members-area/reset-password.html.twig',
                [
                    'code' => $code,
                    'action' => $action,
                    'form' => $form->createView(),
                    'alert' => $alert,
                    'alertMessage' => $alertMessage,
                ]
            )
        );
    }
}
