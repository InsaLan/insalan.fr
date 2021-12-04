<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class PizzaUserOrderRepository extends EntityRepository
{

    public function getByUser(\App\Entity\User $user) {

        $q = $this->createQueryBuilder('uo')
            ->leftJoin('uo.order', 'o')
            ->leftJoin('uo.user', 'u')
            ->leftJoin('uo.pizza', 'p')
            ->addSelect('u')
            ->addSelect('p')
            ->addSelect('o')
            ->where('uo.paymentDone = true')
            ->andWhere('u = :user')
            ->orderBy('uo.createdAt', 'desc')
            ->setParameter('user', $user)
        ;

        return $q->getQuery()->execute();
    }

}
