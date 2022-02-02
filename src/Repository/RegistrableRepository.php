<?php
namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use App\Entity;

class RegistrableRepository extends EntityRepository
{

    public function findThisYearRegistrables() {
        $query = $this->createQueryBuilder('r')
            ->where('r.registrationOpen >= :lastyear')
            ->setParameter('lastyear', (new \DateTime('now'))->modify('-10 month'))
            ->orderBy('r.registrationOpen', 'ASC')
            ->getQuery();
        return $query->getResult();
    }

}
