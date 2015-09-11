<?php

namespace Application\Controller\MembersArea;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RolesController
{
    public function indexAction(Request $request, Application $app)
    {
        $data = array();

        if (! $app['security']->isGranted('ROLE_ROLES_EDITOR')
            && ! $app['security']->isGranted('ROLE_ADMIN')) {
            $app->abort(403);
        }

        $limitPerPage = $request->query->get('limit_per_page', 20);
        $currentPage = $request->query->get('page');

        $roleResults = $app['orm.em']
            ->createQueryBuilder()
            ->select('r')
            ->from('Application\Entity\RoleEntity', 'r')
        ;

        $pagination = $app['paginator']->paginate(
            $roleResults,
            $currentPage,
            $limitPerPage,
            array(
                'route' => 'members-area.roles',
                'defaultSortFieldName' => 'r.priority',
                'defaultSortDirection' => 'desc',
            )
        );

        $data['pagination'] = $pagination;

        return new Response(
            $app['twig']->render(
                'contents/members-area/roles/index.html.twig',
                $data
            )
        );
    }

    public function newAction(Request $request, Application $app)
    {
        $data = array();

        if (! $app['security']->isGranted('ROLE_ROLES_EDITOR')
            && ! $app['security']->isGranted('ROLE_ADMIN')) {
            $app->abort(403);
        }

        $form = $app['form.factory']->create(
            new \Application\Form\Type\RoleType(),
            new \Application\Entity\RoleEntity()
        );

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $roleEntity = $form->getData();

                $app['orm.em']->persist($roleEntity);
                $app['orm.em']->flush();

                $app['flashbag']->add(
                    'success',
                    $app['translator']->trans(
                        'members-area.roles.new.successText'
                    )
                );

                return $app->redirect(
                    $app['url_generator']->generate(
                        'members-area.roles.edit',
                        array(
                            'id' => $roleEntity->getId(),
                        )
                    )
                );
            }
        }

        $data['form'] = $form->createView();

        return new Response(
            $app['twig']->render(
                'contents/members-area/roles/new.html.twig',
                $data
            )
        );
    }

    public function editAction($id, Request $request, Application $app)
    {
        $data = array();

        if (! $app['security']->isGranted('ROLE_ROLES_EDITOR')
            && ! $app['security']->isGranted('ROLE_ADMIN')) {
            $app->abort(403);
        }

        $role = $app['orm.em']->find(
            'Application\Entity\RoleEntity',
            $id
        );

        if (! $role) {
            $app->abort(404);
        }

        $form = $app['form.factory']->create(
            new \Application\Form\Type\RoleType(),
            $role
        );

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $roleEntity = $form->getData();

                $app['orm.em']->persist($roleEntity);
                $app['orm.em']->flush();

                $app['flashbag']->add(
                    'success',
                    $app['translator']->trans(
                        'members-area.roles.edit.successText'
                    )
                );

                return $app->redirect(
                    $app['url_generator']->generate(
                        'members-area.roles.edit',
                        array(
                            'id' => $roleEntity->getId(),
                        )
                    )
                );
            }
        }

        $data['form'] = $form->createView();
        $data['role'] = $role;

        return new Response(
            $app['twig']->render(
                'contents/members-area/roles/edit.html.twig',
                $data
            )
        );
    }

    public function removeAction($id, Request $request, Application $app)
    {
        $data = array();

        if (! $app['security']->isGranted('ROLE_ROLES_EDITOR')
            && ! $app['security']->isGranted('ROLE_ADMIN')) {
            $app->abort(403);
        }

        $role = $app['orm.em']->find(
            'Application\Entity\RoleEntity',
            $id
        );

        if (! $role) {
            $app->abort(404);
        }

        $confirmAction = $app['request']->query->has('action') &&
            $app['request']->query->get('action') == 'confirm'
        ;

        if ($confirmAction) {
            try {
                $app['orm.em']->remove($role);
                $app['orm.em']->flush();

                $app['flashbag']->add(
                    'success',
                    $app['translator']->trans(
                        'members-area.roles.remove.successText'
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
                $app['url_generator']->generate(
                    'members-area.roles'
                )
            );
        }

        $data['role'] = $role;

        return new Response(
            $app['twig']->render(
                'contents/members-area/roles/remove.html.twig',
                $data
            )
        );
    }
}
