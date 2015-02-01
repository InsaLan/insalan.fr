<?php

namespace InsaLan\PizzaBundle\Entity;

use Doctrine\ORM\EntityRepository;

class OrderRepository extends EntityRepository
{
    public function getAvailable()
    {
        $now = new \Datetime();

        $q = $this->createQueryBuilder('o')
            ->where('o.expiration > :now')
            ->setParameter(':now', $now)
            ->orderBy('o.expiration')
        ;

        return $q->getQuery()->execute();
        
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
