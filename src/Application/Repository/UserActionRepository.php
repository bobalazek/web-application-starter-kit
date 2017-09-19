<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * @author Borut Balazek <bobalazek124@gmail.com>
 */
class UserActionRepository extends EntityRepository
{
    /**
     * @return int
     */
    public function countAll()
    {
        return $this->createQueryBuilder('ua')
            ->select('COUNT(ua.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
