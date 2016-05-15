<?php

namespace Application\Controller\MembersArea;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class UserActionsController
{
    /**
     * @param Request     $request
     * @param Application $app
     *
     * @return Response
     */
    public function listAction(Request $request, Application $app)
    {
        if (!$app['security']->isGranted('ROLE_ADMIN')) {
            $app->abort(403);
        }

        $limitPerPage = $request->query->get('limit_per_page', 20);
        $currentPage = $request->query->get('page');

        $userActionResults = $app['orm.em']
            ->createQueryBuilder()
            ->select('ua')
            ->from('Application\Entity\UserActionEntity', 'ua')
            ->leftJoin('ua.user', 'u')
            ->leftJoin('u.profile', 'p')
        ;

        $pagination = $app['paginator']->paginate(
            $userActionResults,
            $currentPage,
            $limitPerPage,
            array(
                'route' => 'members-area.user-actions',
                'defaultSortFieldName' => 'ua.timeCreated',
                'defaultSortDirection' => 'desc',
                'searchFields' => array(
                    'u.username',
                    'u.email',
                    'p.name',
                    'p.firstName',
                    'p.middleName',
                    'p.lastName',
                    'ua.key',
                    'ua.message',
                    'ua.data',
                ),
            )
        );

        return new Response(
            $app['twig']->render(
                'contents/members-area/user-actions/list.html.twig',
                array(
                    'pagination' => $pagination,
                )
            )
        );
    }
}
