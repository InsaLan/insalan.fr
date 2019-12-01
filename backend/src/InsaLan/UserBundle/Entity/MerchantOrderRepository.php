<?php
namespace InsaLan\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;
use InsaLan\UserBundle\Entity;
use InsaLan\TournamentBundle;

class MerchantOrderRepository extends EntityRepository
{

    public function findByPlayer(TournamentBundle\Entity\Player $p) {
        $em = $this->getEntityManager();

        $query = $this->createQueryBuilder('mo')
                    ->join('mo.players', 'p')
                    ->where('p.id = :player')
                    ->setParameter('player', $p)
                    ->getQuery();

        $query->execute();

        return $query->getResult();
    }

}
