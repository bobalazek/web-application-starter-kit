<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * @author Borut Balazek <bobalazek124@gmail.com>
 */
class ErrorRepository extends EntityRepository
{
    /**
     * @return int
     */
    public function countAll()
    {
        return $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
