<?php

namespace Application;

use Silex\Application;
use Knp\Component\Pager\Paginator as KnpPaginator;
use Doctrine\ORM\QueryBuilder;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class Paginator
{
    protected $app;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param $data
     * @param $currentPage
     * @param $limitPerPage
     * @param $options
     */
    public function paginate($data, $currentPage = 1, $limitPerPage = 10, $options = array())
    {
        $paginator = new KnpPaginator();

        if ($currentPage === null) {
            $currentPage = 1;
        }

        if (!isset($options['searchParameter'])) {
            $options['searchParameter'] = 'search';
        }

        // Temporary solution. We'll try to figure out a better one soon!
        $searchFields = isset($options['searchFields'])
            ? $options['searchFields']
            : false
        ;

        $searchValue = $this->app['request']->query->get(
            $options['searchParameter'],
            false
        );

        if ($searchFields && !($data instanceof QueryBuilder)) {
            throw new \Exception('If you want to use search, you MUST use the QueryBuilder!');
        }

        if ($searchFields && $searchValue) {
            if (is_string($searchFields)) {
                $searchFields = explode(',', $searchFields);
            }

            foreach ($searchFields as $searchFieldKey => $searchField) {
                $data
                    ->orWhere($searchField.' LIKE ?'.$searchFieldKey)
                    ->setParameter($searchFieldKey, '%'.$searchValue.'%')
                ;
            }
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
