<?php

namespace Application\Controller\MembersArea;

use Application\Form\Type\UserType;
use Application\Entity\UserEntity;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class UsersController
{
    /**
     * @param Request     $request
     * @param Application $app
     *
     * @return Response
     */
    public function listAction(Request $request, Application $app)
    {
        if (
            !$app['security']->isGranted('ROLE_USERS_EDITOR') &&
            !$app['security']->isGranted('ROLE_ADMIN')
        ) {
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

        $pagination = $app['application.paginator']->paginate(
            $userResults,
            $currentPage,
            $limitPerPage,
            [
                'route' => 'members-area.users',
                'defaultSortFieldName' => 'u.email',
                'defaultSortDirection' => 'asc',
                'searchFields' => [
                    'u.username',
                    'u.email',
                    'u.roles',
                    'p.firstName',
                    'p.lastName',
                ],
            ]
        );

        return new Response(
            $app['twig']->render(
                'contents/members-area/users/list.html.twig',
                [
                    'pagination' => $pagination,
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
    public function newAction(Request $request, Application $app)
    {
        if (
            !$app['security']->isGranted('ROLE_USERS_EDITOR') &&
            !$app['security']->isGranted('ROLE_ADMIN')
        ) {
            $app->abort(403);
        }

        $form = $app['form.factory']->create(
            new UserType($app),
            new UserEntity()
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
                        'A new user was successfully created!'
                    )
                );

                return $app->redirect(
                    $app['url_generator']->generate(
                        'members-area.users.edit',
                        [
                            'id' => $userEntity->getId(),
                        ]
                    )
                );
            }
        }

        return new Response(
            $app['twig']->render(
                'contents/members-area/users/new.html.twig',
                [
                    'form' => $form->createView(),
                ]
            )
        );
    }

    /**
     * @param $id
     * @param Application $app
     *
     * @return Response
     */
    public function detailAction($id, Application $app)
    {
        if (
            !$app['security']->isGranted('ROLE_USERS_EDITOR') &&
            !$app['security']->isGranted('ROLE_ADMIN')
        ) {
            $app->abort(403);
        }

        $user = $app['orm.em']->find('Application\Entity\UserEntity', $id);

        if (!$user) {
            $app->abort(404);
        }

        return new Response(
            $app['twig']->render(
                'contents/members-area/users/detail.html.twig',
                [
                    'user' => $user,
                ]
            )
        );
    }

    public function editAction($id, Request $request, Application $app)
    {
        if (
            !$app['security']->isGranted('ROLE_USERS_EDITOR') &&
            !$app['security']->isGranted('ROLE_ADMIN')
        ) {
            $app->abort(403);
        }

        $user = $app['orm.em']->find(
            'Application\Entity\UserEntity',
            $id
        );

        if (!$user) {
            $app->abort(404);
        }

        $form = $app['form.factory']->create(
            new UserType($app),
            $user
        );

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $userEntity = $form->getData();

                if (
                    $userEntity->isLocked() &&
                    $userEntity->hasRole('ROLE_SUPER_ADMIN')
                ) {
                    $app['flashbag']->add(
                        'danger',
                        $app['translator']->trans(
                            'A super admin user can not be locked!'
                        )
                    );

                    return $app->redirect(
                        $app['url_generator']->generate(
                            'members-area.users.edit',
                            [
                                'id' => $userEntity->getId(),
                            ]
                        )
                    );
                }

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

                /*** Password ***/
                if ($userEntity->getPlainPassword()) {
                    $userEntity->setPlainPassword(
                        $userEntity->getPlainPassword(),
                        $app['security.encoder_factory']
                    );
                }

                $app['orm.em']->persist($userEntity);
                $app['orm.em']->flush();

                $app['flashbag']->add(
                    'success',
                    $app['translator']->trans(
                        'The user was successfully edited!'
                    )
                );

                return $app->redirect(
                    $app['url_generator']->generate(
                        'members-area.users.edit',
                        [
                            'id' => $userEntity->getId(),
                        ]
                    )
                );
            }
        }

        return new Response(
            $app['twig']->render(
                'contents/members-area/users/edit.html.twig',
                [
                    'form' => $form->createView(),
                    'user' => $user,
                ]
            )
        );
    }

    public function removeAction($id, Request $request, Application $app)
    {
        if (
            !$app['security']->isGranted('ROLE_USERS_EDITOR') &&
            !$app['security']->isGranted('ROLE_ADMIN')
        ) {
            $app->abort(403);
        }

        $users = [];
        $ids = $request->query->get('ids', false);
        $idsExploded = explode(',', $ids);
        foreach ($idsExploded as $singleId) {
            $singleEntity = $app['orm.em']->find(
                'Application\Entity\UserEntity',
                $singleId
            );

            if ($singleEntity) {
                $users[] = $singleEntity;
            }
        }

        $user = $app['orm.em']->find('Application\Entity\UserEntity', $id);

        if (
            (
                !$user &&
                $ids === false
            ) ||
            (
                empty($users) &&
                $ids !== false
            )
        ) {
            $app->abort(404);
        }

        $confirmAction = $app['request']->query->has('action') && $app['request']->query->get('action') == 'confirm';

        if ($confirmAction) {
            try {
                if (!empty($users)) {
                    foreach ($users as $user) {
                        $app['orm.em']->remove($user);
                    }
                } else {
                    $app['orm.em']->remove($user);
                }

                $app['orm.em']->flush();

                $app['flashbag']->add(
                    'success',
                    $app['translator']->trans(
                        'The user "%user%" was successfully removed!',
                        [
                            '%user%' => $user,
                        ]
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

        return new Response(
            $app['twig']->render(
                'contents/members-area/users/remove.html.twig',
                [
                    'user' => $user,
                    'users' => $users,
                    'ids' => $ids,
                ]
            )
        );
    }
}
