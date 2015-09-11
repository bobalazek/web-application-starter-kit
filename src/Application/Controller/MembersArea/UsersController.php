<?php

namespace Application\Controller\MembersArea;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UsersController
{
    public function indexAction(Request $request, Application $app)
    {
        $data = array();

        if (! $app['security']->isGranted('ROLE_USERS_EDITOR')
            && ! $app['security']->isGranted('ROLE_ADMIN')) {
            $app->abort(403);
        }

        $limitPerPage = $request->query->get('limit_per_page', 20);
        $currentPage = $request->query->get('page');

        $userResults = $app['orm.em']
            ->createQueryBuilder()
            ->select('u')
            ->from('Application\Entity\UserEntity', 'u')
            ->leftJoin('u.profile', 'p')
        ;

        $pagination = $app['paginator']->paginate(
            $userResults,
            $currentPage,
            $limitPerPage,
            array(
                'route' => 'members-area.users',
                'defaultSortFieldName' => 'u.email',
                'defaultSortDirection' => 'asc',
            )
        );

        $data['pagination'] = $pagination;

        return new Response(
            $app['twig']->render(
                'contents/members-area/users/index.html.twig',
                $data
            )
        );
    }

    public function newAction(Request $request, Application $app)
    {
        $data = array();

        if (! $app['security']->isGranted('ROLE_USERS_EDITOR')
            && ! $app['security']->isGranted('ROLE_ADMIN')) {
            $app->abort(403);
        }

        $form = $app['form.factory']->create(
            new \Application\Form\Type\UserType(),
            new \Application\Entity\UserEntity()
        );

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $userEntity = $form->getData();

                /*** Image ***/
                $userEntity
                    ->getProfile()
                    ->setImageUploadPath($app['baseUrl'].'/assets/uploads/')
                    ->setImageUploadDir(WEB_DIR.'/assets/uploads/')
                    ->imageUpload()
                ;

                /*** Password ***/
                $userEntity->setPlainPassword(
                    $userEntity->getPlainPassword(), // This getPassword() is here just the plain password. That's why we need to convert it
                    $app['security.encoder_factory']
                );

                $app['orm.em']->persist($userEntity);
                $app['orm.em']->flush();

                $app['flashbag']->add(
                    'success',
                    $app['translator']->trans(
                        'members-area.users.new.successText'
                    )
                );

                return $app->redirect(
                    $app['url_generator']->generate(
                        'members-area.users.edit',
                        array(
                            'id' => $userEntity->getId(),
                        )
                    )
                );
            }
        }

        $data['form'] = $form->createView();

        return new Response(
            $app['twig']->render(
                'contents/members-area/users/new.html.twig',
                $data
            )
        );
    }

    public function detailAction($id, Request $request, Application $app)
    {
        $data = array();

        if (! $app['security']->isGranted('ROLE_USERS_EDITOR')
            && ! $app['security']->isGranted('ROLE_ADMIN')) {
            $app->abort(403);
        }

        $user = $app['orm.em']->find('Application\Entity\UserEntity', $id);

        if (! $user) {
            $app->abort(404);
        }

        $data['user'] = $user;

        return new Response(
            $app['twig']->render(
                'contents/members-area/users/detail.html.twig',
                $data
            )
        );
    }

    public function editAction($id, Request $request, Application $app)
    {
        $data = array();

        if (! $app['security']->isGranted('ROLE_USERS_EDITOR')
            && ! $app['security']->isGranted('ROLE_ADMIN')) {
            $app->abort(403);
        }

        $user = $app['orm.em']->find(
            'Application\Entity\UserEntity',
            $id
        );

        if (! $user) {
            $app->abort(404);
        }

        $form = $app['form.factory']->create(
            new \Application\Form\Type\UserType(),
            $user
        );

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $userEntity = $form->getData();

                $roleSuperAdmin = $app['orm.em']
                    ->getRepository('Application\Entity\RoleEntity')
                    ->findOneByRole('ROLE_SUPER_ADMIN')
                ;

                if ($userEntity->hasRole($roleSuperAdmin) &&
                    $userEntity->isLocked()) {
                    $app['flashbag']->add(
                        'danger',
                        $app['translator']->trans(
                            'members-area.users.edit.superAdminLockedText'
                        )
                    );

                    return $app->redirect(
                        $app['url_generator']->generate(
                            'members-area.users.edit',
                            array(
                                'id' => $userEntity->getId(),
                            )
                        )
                    );
                }

                /*** Image ***/
                $userEntity
                    ->getProfile()
                    ->setImageUploadPath($app['baseUrl'].'/assets/uploads/')
                    ->setImageUploadDir(WEB_DIR.'/assets/uploads/')
                    ->imageUpload()
                ;

                /*** Password ***/
                if ($userEntity->getPlainPassword()) {
                    $userEntity->setPlainPassword(
                        $userEntity->getPlainPassword(), // This getPassword() is here just the plain password. That's why we need to convert it
                        $app['security.encoder_factory']
                    );
                }

                $app['orm.em']->persist($userEntity);
                $app['orm.em']->flush();

                $app['flashbag']->add(
                    'success',
                    $app['translator']->trans(
                        'members-area.users.edit.successText'
                    )
                );

                return $app->redirect(
                    $app['url_generator']->generate(
                        'members-area.users.edit',
                        array(
                            'id' => $userEntity->getId(),
                        )
                    )
                );
            }
        }

        $data['form'] = $form->createView();

        $data['user'] = $user;

        return new Response(
            $app['twig']->render(
                'contents/members-area/users/edit.html.twig',
                $data
            )
        );
    }

    public function removeAction($id, Request $request, Application $app)
    {
        $data = array();

        if (! $app['security']->isGranted('ROLE_USERS_EDITOR')
            && ! $app['security']->isGranted('ROLE_ADMIN')) {
            $app->abort(403);
        }

        $user = $app['orm.em']->find('Application\Entity\UserEntity', $id);

        if (! $user) {
            $app->abort(404);
        }

        $confirmAction = $app['request']->query->has('action') && $app['request']->query->get('action') == 'confirm';

        if ($confirmAction) {
            try {
                $app['orm.em']->remove($user);
                $app['orm.em']->flush();

                $app['flashbag']->add(
                    'success',
                    $app['translator']->trans(
                        'members-area.users.remove.successText'
                    )
                );
            } catch (\Exception $e) {
                $app['flashbag']->add(
                    'danger',
                    $app['translator']->trans(
                        $e->getMessage()
                    )
                );
            }

            return $app->redirect(
                $app['url_generator']->generate('members-area.users')
            );
        }

        $data['user'] = $user;

        return new Response(
            $app['twig']->render('contents/members-area/users/remove.html.twig', $data)
        );
    }
}
