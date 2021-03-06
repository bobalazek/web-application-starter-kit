<?php

namespace Application\Controller\MembersArea;

use Application\Form\Type\PostType;
use Application\Entity\PostEntity;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Borut Balazek <bobalazek124@gmail.com>
 */
class PostsController
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
            !$app['security.authorization_checker']->isGranted('ROLE_POSTS_EDITOR') &&
            !$app['security.authorization_checker']->isGranted('ROLE_ADMIN')
        ) {
            $app->abort(403);
        }

        $limitPerPage = $request->query->get('limit_per_page', 20);
        $currentPage = $request->query->get('page');

        $postResults = $app['orm.em']
            ->createQueryBuilder()
            ->select('p')
            ->from('Application\Entity\PostEntity', 'p')
            ->leftJoin('p.user', 'u')
        ;

        $pagination = $app['application.paginator']->paginate(
            $postResults,
            $currentPage,
            $limitPerPage,
            [
                'route' => 'members-area.posts',
                'defaultSortFieldName' => 'p.timeCreated',
                'defaultSortDirection' => 'desc',
                'searchFields' => [
                    'p.title',
                    'p.content',
                    'u.username',
                    'u.email',
                ],
            ]
        );

        return new Response(
            $app['twig']->render(
                'contents/members-area/posts/list.html.twig',
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
            !$app['security.authorization_checker']->isGranted('ROLE_POSTS_EDITOR') &&
            !$app['security.authorization_checker']->isGranted('ROLE_ADMIN')
        ) {
            $app->abort(403);
        }

        $form = $app['form.factory']->create(
            PostType::class,
            new PostEntity()
        );

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $postEntity = $form->getData();

                /*** Image ***/
                $postEntity
                    ->setImageUploadPath($app['baseUrl'].'/assets/uploads/')
                    ->setImageUploadDir(WEB_DIR.'/assets/uploads/')
                    ->imageUpload()
                ;

                $app['orm.em']->persist($postEntity);
                $app['orm.em']->flush();

                $app['flashbag']->add(
                    'success',
                    $app['translator']->trans(
                        'A new post was successfully created!'
                    )
                );

                return $app->redirect(
                    $app['url_generator']->generate(
                        'members-area.posts.edit',
                        [
                            'id' => $postEntity->getId(),
                        ]
                    )
                );
            }
        }

        return new Response(
            $app['twig']->render(
                'contents/members-area/posts/new.html.twig',
                [
                    'form' => $form->createView(),
                ]
            )
        );
    }

    /**
     * @param $id
     * @param Request     $request
     * @param Application $app
     *
     * @return Response
     */
    public function editAction($id, Request $request, Application $app)
    {
        if (
            !$app['security.authorization_checker']->isGranted('ROLE_POSTS_EDITOR') &&
            !$app['security.authorization_checker']->isGranted('ROLE_ADMIN')
        ) {
            $app->abort(403);
        }

        $post = $app['orm.em']->find('Application\Entity\PostEntity', $id);

        if (!$post) {
            $app->abort(404);
        }

        $form = $app['form.factory']->create(
            PostType::class,
            $post
        );

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $postEntity = $form->getData();

                if ($postEntity->getRemoveImage()) {
                    $postEntity->setImageUrl(null);
                }

                /*** Image ***/
                $postEntity
                    ->setImageUploadPath($app['baseUrl'].'/assets/uploads/')
                    ->setImageUploadDir(WEB_DIR.'/assets/uploads/')
                    ->imageUpload()
                ;

                $app['orm.em']->persist($postEntity);
                $app['orm.em']->flush();

                $app['flashbag']->add(
                    'success',
                    $app['translator']->trans(
                        'The post was successfully edited!'
                    )
                );

                return $app->redirect(
                    $app['url_generator']->generate(
                        'members-area.posts.edit',
                        [
                            'id' => $postEntity->getId(),
                        ]
                    )
                );
            }
        }

        return new Response(
            $app['twig']->render(
                'contents/members-area/posts/edit.html.twig',
                [
                    'form' => $form->createView(),
                    'post' => $post,
                ]
            )
        );
    }

    /**
     * @param $id
     * @param Request     $request
     * @param Application $app
     *
     * @return Response
     */
    public function removeAction($id, Request $request, Application $app)
    {
        if (
            !$app['security.authorization_checker']->isGranted('ROLE_POSTS_EDITOR') &&
            !$app['security.authorization_checker']->isGranted('ROLE_ADMIN')
        ) {
            $app->abort(403);
        }

        $posts = [];
        $ids = $request->query->get('ids', false);
        $idsExploded = explode(',', $ids);
        foreach ($idsExploded as $singleId) {
            $singleEntity = $app['orm.em']->find(
                'Application\Entity\PostEntity',
                $singleId
            );

            if ($singleEntity) {
                $posts[] = $singleEntity;
            }
        }

        $post = $app['orm.em']->find('Application\Entity\PostEntity', $id);

        if (
            (
                !$post &&
                $ids === false
            ) ||
            (
                empty($posts) &&
                $ids !== false
            )
        ) {
            $app->abort(404);
        }

        $confirmAction = $request->query->has('action')
            && $request->query->get('action') == 'confirm';

        if ($confirmAction) {
            try {
                if (!empty($posts)) {
                    foreach ($posts as $post) {
                        $app['orm.em']->remove($post);
                    }
                } else {
                    $app['orm.em']->remove($post);
                }

                $app['orm.em']->flush();

                $app['flashbag']->add(
                    'success',
                    $app['translator']->trans(
                        'The post "%post%" was successfully removed!',
                        [
                            '%post%' => $post,
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
                $app['url_generator']->generate(
                    'members-area.posts'
                )
            );
        }

        return new Response(
            $app['twig']->render(
                'contents/members-area/posts/remove.html.twig',
                [
                    'post' => $post,
                    'posts' => $posts,
                    'ids' => $ids,
                ]
            )
        );
    }
}
