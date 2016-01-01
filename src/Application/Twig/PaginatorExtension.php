<?php

namespace Application\Twig;

use Silex\Application;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
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
     * @return Twig_SimpleFunction[]
     */
    public function getFunctions()
    {
        return array(
            'paginator_limit_per_page_render' => new \Twig_Function_Method(
                $this,
                'paginatorLimitPerPageRender',
                array(
                    'is_safe' => array('html'),
                )
            ),
            'paginator_search_render' => new \Twig_Function_Method(
                $this,
                'paginatorSearchRender',
                array(
                    'is_safe' => array('html'),
                )
            ),
            'paginator_top_render' => new \Twig_Function_Method(
                $this,
                'paginatorTopRender',
                array(
                    'is_safe' => array('html'),
                )
            ),
            'paginator_pagination_render' => new \Twig_Function_Method(
                $this,
                'paginatorPaginationRender',
                array(
                    'is_safe' => array('html'),
                )
            ),
            'paginator_results_text_render' => new \Twig_Function_Method(
                $this,
                'paginatorResultsTextRender',
                array(
                    'is_safe' => array('html'),
                )
            ),
            'paginator_bottom_render' => new \Twig_Function_Method(
                $this,
                'paginatorBottomRender',
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

    /**
     * @param $pagination
     *
     * @return string
     */
    public function paginatorPaginationRender($pagination)
    {
        $output = '';

        $paginationData = $pagination->getPaginationData();
        $maxPageRange = isset($paginationData['pageRangeLimit'])
            ? intval($paginationData['pageRangeLimit'])
            : 10
        ;
        $route = $paginationData['route'];
        $routeParameters = $this->app['request']->query->all();
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
                'twig/paginator/pagination.html.twig',
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

    /**
     * @param $pagination
     *
     * @return string
     */
    public function paginatorResultsTextRender($pagination)
    {
        $output = '';

        $paginationData = $pagination->getPaginationData();
        $pageCount = ceil(
            intval($paginationData['totalCount']) /
            intval($paginationData['numItemsPerPage'])
        );
        $currentPage = intval($paginationData['current']);
        $total = $paginationData['totalCount'];

        if ($total > 0 && $currentPage <= $pageCount) {
            $from = (($currentPage -1) * $paginationData['numItemsPerPage']) + 1;
            $to = $currentPage * $paginationData['numItemsPerPage'] > $total
                ? $total
                : $currentPage * $paginationData['numItemsPerPage']
            ;

            $output = $this->app['twig']->render(
                'twig/paginator/results-text.html.twig',
                array(
                    'app' => $this->app,
                    'from' => $from,
                    'to' => $to,
                    'total' => $total,
                )
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
            array(
                'pagination' => $pagination,
            )
        );
    }

    /**
     * @param $pagination
     *
     * @return string
     */
    public function paginatorLimitPerPageRender($pagination)
    {
        $output = '';

        $paginationData = $pagination->getPaginationData();

        if ($paginationData['totalCount'] > 0) {
            $output = $this->app['twig']->render(
                'twig/paginator/limit-per-page.html.twig',
                array(
                    'app' => $this->app,
                    'pagination' => $pagination,
                )
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
            array(
                'app' => $this->app,
                'pagination' => $pagination,
            )
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
            array(
                'pagination' => $pagination,
            )
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
        if (isset($paginationData['routeParameters'])) {
            $routeParameters = array_merge(
                $routeParameters,
                $paginationData['routeParameters']
            );
        }
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

        return '<a href="'.$url.'">'.
            $text.
            ($showIcon ? ' <i class="fa fa-chevron-'.$icon.'"></i>' : '').
        '</a>';
    }
}
