<?php
namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use App\Entity;
use App;

class UserMerchantOrderRepository extends EntityRepository
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
