<?php

namespace InsaLan\PizzaBundle\Entity;

use Doctrine\ORM\EntityRepository;

class OrderRepository extends EntityRepository
{
    public function getCurrent()
    {
        $now = new \Datetime();

        $q = $this->createQueryBuilder('o')
            ->where('o.createdAt >= :now')
            ->andWhere('o.expiration > :now')
            ->setParameter(':now', $now)
            ->setMaxResults(1)
        ;

        $orders = $q->getQuery()->execute();
        if ($orders) return $orders[0];
        return null;
    }

    public function getAll()
    {
        $q = $this->createQueryBuilder('o')
            ->leftJoin('o.orders', 'uo')
            ->leftJoin('uo.user', 'u')
            ->leftJoin('uo.pizza', 'p')
            ->addSelect('uo')
            ->addSelect('u')
            ->addSelect('p')
            ->orderBy('o.id', 'desc')
            ->orderBy('u.username', 'asc')
        ;

        return $q->getQuery()->execute();
    }
}
