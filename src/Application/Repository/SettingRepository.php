<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * @author Borut Balazek <bobalazek124@gmail.com>
 */
class SettingRepository extends EntityRepository
{
    /**
     * @return int
     */
    public function countAll()
    {
        return $this->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
