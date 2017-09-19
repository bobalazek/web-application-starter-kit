<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * @author Borut Balazek <bobalazek124@gmail.com>
 */
class PostMetaRepository extends EntityRepository
{
    /**
     * @return int
     */
    public function countAll()
    {
        return $this->createQueryBuilder('pm')
            ->select('COUNT(pm.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
