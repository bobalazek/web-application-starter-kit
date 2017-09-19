<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * @author Borut Balazek <bobalazek124@gmail.com>
 */
class PostRepository extends EntityRepository
{
    /**
     * @return int
     */
    public function countAll()
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
