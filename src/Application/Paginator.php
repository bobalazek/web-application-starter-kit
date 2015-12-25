<?php

namespace Application;

use Silex\Application;
use Knp\Component\Pager\Paginator as KnpPaginator;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class Paginator
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function paginate($data, $currentPage = 1, $limitPerPage = 10, $options = array())
    {
        $paginator = new KnpPaginator();

        if ($currentPage == null) {
            $currentPage = 1;
        }

        $pagination = $paginator->paginate(
            $data,
            $currentPage,
            $limitPerPage,
            $options
        );

        return $pagination;
    }
}
