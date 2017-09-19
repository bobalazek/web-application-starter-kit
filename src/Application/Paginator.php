<?php

namespace Application;

use Silex\Application;
use Knp\Component\Pager\Paginator as KnpPaginator;
use Doctrine\ORM\QueryBuilder;

/**
 * @author Borut Balazek <bobalazek124@gmail.com>
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
     * @param mixed $data
     * @param int   $currentPage
     * @param int   $limitPerPage
     * @param array $options
     *
     * @throws \Exception if searchFields $option key is set without the $data variable being type QueryBuilder
     */
    public function paginate($data, $currentPage = 1, $limitPerPage = 10, $options = [])
    {
        $paginator = new KnpPaginator();
        $request = $this->app['request_stack']->getCurrentRequest();

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

        $searchValue = $request->query->get(
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

        return $paginator->paginate(
            $data,
            $currentPage,
            $limitPerPage,
            $options
        );
    }
}
