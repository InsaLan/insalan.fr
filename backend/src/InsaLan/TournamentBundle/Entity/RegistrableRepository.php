<?php
namespace InsaLan\TournamentBundle\Entity;

use Doctrine\ORM\EntityRepository;
use InsaLan\TournamentBundle\Entity;

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
