<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * EditionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ArchivesEditionRepository extends EntityRepository
{
	    public function getEditions() {
        $query = $this->createQueryBuilder('e')
            ->orderBy('e.year', 'DESC')
            ->getQuery();
        return $query->getResult();
    }


}
