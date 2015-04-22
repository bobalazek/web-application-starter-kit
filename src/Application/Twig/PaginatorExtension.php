<?php

namespace Application\Twig;

use Silex\Application;

class PaginatorExtension extends \Twig_Extension
{
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function getName()
    {
        return 'application/paginator';
    }

    public function getFunctions()
    {
        return array(
            'paginator_render' => new \Twig_Function_Method(
                $this,
                'paginatorRender',
                array(
                    'is_safe' => array('html'),
                )
            ),
            'paginator_sortable' => new \Twig_Function_Method(
                $this,
                'paginatorSortable',
                array(
                    'is_safe' => array('html'),
                )
            ),
        );
    }

    public function paginatorRender($pagination)
    {
        $output = '';

        $paginationData = $pagination->getPaginationData();
        $maxPageRange = isset($paginationData['pageRangeLimit'])
            ? intval($paginationData['pageRangeLimit'])
            : 10
        ;
        $route = $paginationData['route'];
        $routeParameters = $this->app['request']->query->all();
        $pageCount = ceil(
            intval($paginationData['totalCount']) /
            intval($paginationData['numItemsPerPage'])
        );
        $currentPage = intval($paginationData['current']);

        if ($pageCount > 1) {
            $pageRange = range(1, $pageCount);

            // Page range by max page numbers
            $pageRangeTmp = array();
            $rangeFrom = $currentPage - ceil($maxPageRange / 2);
            $rangeTo = $currentPage + ceil($maxPageRange / 2);

            foreach (range($rangeFrom, $rangeTo) as $singleRangePage) {
                if (in_array($singleRangePage, $pageRange)) {
                    $pageRangeTmp[] = $singleRangePage;
                }
            }

            $pageRange = $pageRangeTmp;
            // Page range by max page numbers /END

            // Prev
            if ($currentPage > 1) {
                $routeParameters = array_merge(
                    $routeParameters,
                    array(
                        $pagination->getPaginatorOption('pageParameterName') => $currentPage - 1,
                    )
                );

                $prevUrl = $this->app['url_generator']->generate(
                    $route,
                    $routeParameters
                );
            } else {
                $prevUrl = '#';
            }
            // Prev /END

            // Next
            if ($currentPage < $pageCount) {
                $routeParameters = array_merge(
                    $routeParameters,
                    array(
                        $pagination->getPaginatorOption('pageParameterName') => $currentPage + 1,
                    )
                );

                $nextUrl = $this->app['url_generator']->generate(
                    $route,
                    $routeParameters
                );
            } else {
                $nextUrl = '#';
            }
            // Next /END

            $output = $this->app['twig']->render(
                'twig/paginator.html.twig',
                array(
                    'app' => $this->app,
                    'prevUrl' => $prevUrl,
                    'nextUrl' => $nextUrl,
                    'pageRange' => $pageRange,
                    'routeParameters' => $routeParameters,
                    'pagination' => $pagination,
                    'route' => $route,
                    'currentPage' => $currentPage,
                    'pageCount' => $pageCount,
                )
            );
        }

        return $output;
    }

    public function paginatorSortable($pagination, $text = '', $key = '')
    {
        $output = '';

        $text = $this->app['translator']->trans($text);

        $sortDirectionParameterName = $pagination->getPaginatorOption('sortDirectionParameterName');
        $direction = isset($sortDirectionParameterName)
            ? $this->app['request']->query->get($sortDirectionParameterName)
            : 'asc'
        ;

        $direction = $direction == 'asc'
            ? 'desc'
            : 'asc'
        ;

        $paginationData = $pagination->getPaginationData();
        $route = $paginationData['route'];
        $routeParameters = $this->app['request']->query->all();
        $routeParameters = array_merge(
            $routeParameters,
            array(
                $pagination->getPaginatorOption('pageParameterName') => 1,
                $pagination->getPaginatorOption('sortFieldParameterName') => $key,
                $pagination->getPaginatorOption('sortDirectionParameterName') => $direction,
            )
        );

        $url = $this->app['url_generator']->generate(
            $route,
            $routeParameters
        );

        $icon = $direction == 'asc'
            ? 'down'
            : 'up'
        ;

        $showIcon = $this->app['request']->query->get(
            $pagination->getPaginatorOption('sortFieldParameterName'),
            $paginationData['defaultSortFieldName']
        ) == $key
            ? true
            : false
        ;

        $output = '<a href="'.$url.'">'.
            $text.
            ($showIcon ? ' <i class="fa fa-chevron-'.$icon.'"></i>' : '').
        '</a>';

        return $output;
    }
}
