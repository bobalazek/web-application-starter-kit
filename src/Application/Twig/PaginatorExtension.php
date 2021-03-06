<?php

namespace Application\Twig;

use Silex\Application;

/**
 * @author Borut Balazek <bobalazek124@gmail.com>
 */
class PaginatorExtension extends \Twig_Extension
{
    private $app;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'application/paginator';
    }

    /**
     * @return \Twig_SimpleFunction[]
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'paginator_limit_per_page_render',
                [
                    $this,
                    'paginatorLimitPerPageRender',
                ],
                [
                    'is_safe' => ['html'],
                ]
            ),
            new \Twig_SimpleFunction(
                'paginator_search_render',
                [
                    $this,
                    'paginatorSearchRender',
                ],
                [
                    'is_safe' => ['html'],
                ]
            ),
            new \Twig_SimpleFunction(
                'paginator_top_render',
                [
                    $this,
                    'paginatorTopRender',
                ],
                [
                    'is_safe' => ['html'],
                ]
            ),
            new \Twig_SimpleFunction(
                'paginator_pagination_render',
                [
                    $this,
                    'paginatorPaginationRender',
                ],
                [
                    'is_safe' => ['html'],
                ]
            ),
            new \Twig_SimpleFunction(
                'paginator_results_text_render',
                [
                    $this,
                    'paginatorResultsTextRender',
                ],
                [
                    'is_safe' => ['html'],
                ]
            ),
            new \Twig_SimpleFunction(
                'paginator_bottom_render',
                [
                    $this,
                    'paginatorBottomRender',
                ],
                [
                    'is_safe' => ['html'],
                ]
            ),
            new \Twig_SimpleFunction(
                'paginator_sortable',
                [
                    $this,
                    'paginatorSortable',
                ],
                [
                    'is_safe' => ['html'],
                ]
            ),
        ];
    }

    /**
     * @param $pagination
     *
     * @return string
     */
    public function paginatorPaginationRender($pagination)
    {
        if (!$pagination) {
            return '';
        }

        $request = $this->app['request_stack']->getCurrentRequest();

        $output = '';

        $paginationData = $pagination->getPaginationData();
        $maxPageRange = isset($paginationData['pageRangeLimit'])
            ? intval($paginationData['pageRangeLimit'])
            : 10;
        $route = $paginationData['route'];
        $routeParameters = $request->query->all();
        if (isset($paginationData['routeParameters'])) {
            $routeParameters = array_merge(
                $routeParameters,
                $paginationData['routeParameters']
            );
        }
        $pageCount = ceil(
            intval($paginationData['totalCount']) /
            intval($paginationData['numItemsPerPage'])
        );
        $currentPage = intval($paginationData['current']);

        if ($pageCount > 1) {
            $pageRange = range(1, $pageCount);

            // Page range by max page numbers
            $pageRangeTmp = [];
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
                    [
                        $pagination->getPaginatorOption('pageParameterName') => $currentPage - 1,
                    ]
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
                    [
                        $pagination->getPaginatorOption('pageParameterName') => $currentPage + 1,
                    ]
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
                'twig/paginator/pagination.html.twig',
                [
                    'app' => $this->app,
                    'prevUrl' => $prevUrl,
                    'nextUrl' => $nextUrl,
                    'pageRange' => $pageRange,
                    'routeParameters' => $routeParameters,
                    'pagination' => $pagination,
                    'route' => $route,
                    'currentPage' => $currentPage,
                    'pageCount' => $pageCount,
                ]
            );
        }

        return $output;
    }

    /**
     * @param $pagination
     *
     * @return string
     */
    public function paginatorResultsTextRender($pagination)
    {
        if (!$pagination) {
            return '';
        }

        $output = '';

        $paginationData = $pagination->getPaginationData();
        $pageCount = ceil(
            intval($paginationData['totalCount']) /
            intval($paginationData['numItemsPerPage'])
        );
        $currentPage = intval($paginationData['current']);
        $total = $paginationData['totalCount'];

        if ($total > 0 && $currentPage <= $pageCount) {
            $from = (($currentPage - 1) * $paginationData['numItemsPerPage']) + 1;
            $to = $currentPage * $paginationData['numItemsPerPage'] > $total
                ? $total
                : $currentPage * $paginationData['numItemsPerPage']
            ;

            $output = $this->app['twig']->render(
                'twig/paginator/results-text.html.twig',
                [
                    'app' => $this->app,
                    'from' => $from,
                    'to' => $to,
                    'total' => $total,
                ]
            );
        }

        return $output;
    }

    /**
     * @param $pagination
     *
     * @return string
     */
    public function paginatorBottomRender($pagination)
    {
        return $this->app['twig']->render(
            'twig/paginator/_bottom.html.twig',
            [
                'pagination' => $pagination,
            ]
        );
    }

    /**
     * @param $pagination
     *
     * @return string
     */
    public function paginatorLimitPerPageRender($pagination)
    {
        if (!$pagination) {
            return '';
        }

        $output = '';

        $paginationData = $pagination->getPaginationData();

        if ($paginationData['totalCount'] > 0) {
            $output = $this->app['twig']->render(
                'twig/paginator/limit-per-page.html.twig',
                [
                    'app' => $this->app,
                    'pagination' => $pagination,
                ]
            );
        }

        return $output;
    }

    /**
     * @param $pagination
     *
     * @return string
     */
    public function paginatorSearchRender($pagination)
    {
        return $this->app['twig']->render(
            'twig/paginator/search.html.twig',
            [
                'app' => $this->app,
                'pagination' => $pagination,
            ]
        );
    }

    /**
     * @param $pagination
     *
     * @return string
     */
    public function paginatorTopRender($pagination)
    {
        return $this->app['twig']->render(
            'twig/paginator/_top.html.twig',
            [
                'pagination' => $pagination,
            ]
        );
    }

    /**
     * @param $pagination
     * @param $text
     * @param $key
     *
     * @return string
     */
    public function paginatorSortable($pagination, $text = '', $key = '')
    {
        if (!$pagination) {
            return '';
        }

        $request = $this->app['request_stack']->getCurrentRequest();

        $text = $this->app['translator']->trans($text);
        $sortDirectionParameterName = $pagination->getPaginatorOption('sortDirectionParameterName');
        $direction = isset($sortDirectionParameterName)
            ? $request->query->get($sortDirectionParameterName)
            : 'asc'
        ;

        $direction = $direction == 'asc'
            ? 'desc'
            : 'asc'
        ;

        $paginationData = $pagination->getPaginationData();
        $route = $paginationData['route'];
        $routeParameters = $request->query->all();
        if (isset($paginationData['routeParameters'])) {
            $routeParameters = array_merge(
                $routeParameters,
                $paginationData['routeParameters']
            );
        }
        $routeParameters = array_merge(
            $routeParameters,
            [
                $pagination->getPaginatorOption('pageParameterName') => 1,
                $pagination->getPaginatorOption('sortFieldParameterName') => $key,
                $pagination->getPaginatorOption('sortDirectionParameterName') => $direction,
            ]
        );

        $url = $this->app['url_generator']->generate(
            $route,
            $routeParameters
        );

        $icon = $direction == 'asc'
            ? 'down'
            : 'up'
        ;

        $showIcon = $request->query->get(
            $pagination->getPaginatorOption('sortFieldParameterName'),
            $paginationData['defaultSortFieldName']
        ) == $key
            ? true
            : false
        ;

        return '<a href="'.$url.'">'.
            $text.
            ($showIcon ? ' <i class="fa fa-chevron-'.$icon.'"></i>' : '').
        '</a>';
    }
}
