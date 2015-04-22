<?php

namespace Application;

class Paginator
{
    protected $app;

    public function __construct(\Silex\Application $app)
    {
        $this->app = $app;
    }

    public function paginate($data, $currentPage = 1, $limitPerPage = 10, $options = array())
    {
        $paginator = new \Knp\Component\Pager\Paginator();

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
