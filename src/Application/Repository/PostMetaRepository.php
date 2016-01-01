<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class PostMetaRepository extends EntityRepository
{
    public function countAll()
    {
        return $this->createQueryBuilder('pm')
            ->select('COUNT(pm.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
