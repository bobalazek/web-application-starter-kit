<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository
    extends EntityRepository
{
    public function countAll()
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
