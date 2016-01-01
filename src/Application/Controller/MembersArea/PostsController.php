<?php

namespace Application\Controller\MembersArea;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Application\Form\Type\PostType;
use Application\Entity\PostEntity;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class PostsController
{
    /**
     * @param Request     $request
     * @param Application $app
     *
     * @return Response
     */
    public function indexAction(Request $request, Application $app)
    {
        $data = array();

        if (
            !$app['security']->isGranted('ROLE_POSTS_EDITOR') &&
            !$app['security']->isGranted('ROLE_ADMIN')
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

        $pagination = $app['paginator']->paginate(
            $postResults,
            $currentPage,
            $limitPerPage,
            array(
                'route' => 'members-area.posts',
                'defaultSortFieldName' => 'p.timeCreated',
                'defaultSortDirection' => 'desc',
                'searchFields' => array(
                    'p.title',
                    'p.content',
                    'u.username',
                    'u.email',
                ),
            )
        );

        $data['pagination'] = $pagination;

        return new Response(
            $app['twig']->render(
                'contents/members-area/posts/index.html.twig',
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
    public function newAction(Request $request, Application $app)
    {
        $data = array();

        if (
            !$app['security']->isGranted('ROLE_POSTS_EDITOR') &&
            !$app['security']->isGranted('ROLE_ADMIN')
        ) {
            $app->abort(403);
        }

        $form = $app['form.factory']->create(
            new PostType(),
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
                        'members-area.posts.new.successText'
                    )
                );

                return $app->redirect(
                    $app['url_generator']->generate(
                        'members-area.posts.edit',
                        array(
                            'id' => $postEntity->getId(),
                        )
                    )
                );
            }
        }

        $data['form'] = $form->createView();

        return new Response(
            $app['twig']->render(
                'contents/members-area/posts/new.html.twig',
                $data
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
        $data = array();

        if (
            !$app['security']->isGranted('ROLE_POSTS_EDITOR') &&
            !$app['security']->isGranted('ROLE_ADMIN')
        ) {
            $app->abort(403);
        }

        $post = $app['orm.em']->find('Application\Entity\PostEntity', $id);

        if (!$post) {
            $app->abort(404);
        }

        $form = $app['form.factory']->create(
            new PostType(),
            $post
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
                        'members-area.posts.edit.successText'
                    )
                );

                return $app->redirect(
                    $app['url_generator']->generate(
                        'members-area.posts.edit',
                        array(
                            'id' => $postEntity->getId(),
                        )
                    )
                );
            }
        }

        $data['form'] = $form->createView();
        $data['post'] = $post;

        return new Response(
            $app['twig']->render(
                'contents/members-area/posts/edit.html.twig',
                $data
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
        $data = array();

        if (
            !$app['security']->isGranted('ROLE_POSTS_EDITOR') &&
            !$app['security']->isGranted('ROLE_ADMIN')
        ) {
            $app->abort(403);
        }

        $posts = array();
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

        $confirmAction = $app['request']->query->has('action') &&
            $app['request']->query->get('action') == 'confirm'
        ;

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
                        'members-area.posts.remove.successText'
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

        $data['post'] = $post;
        $data['posts'] = $posts;
        $data['ids'] = $ids;

        return new Response(
            $app['twig']->render(
                'contents/members-area/posts/remove.html.twig',
                $data
            )
        );
    }
}
