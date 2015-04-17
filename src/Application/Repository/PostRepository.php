<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;

class PostRepository
    extends EntityRepository
{
    public function countAll()
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
