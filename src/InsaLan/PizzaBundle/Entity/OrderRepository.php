<?php

namespace InsaLan\PizzaBundle\Entity;

use Doctrine\ORM\EntityRepository;

class OrderRepository extends EntityRepository
{
    public function getAvailable()
    {
        $now = new \Datetime();

        $q = $this->createQueryBuilder('o')
            ->leftJoin('o.orders', 'uo')
            ->addSelect('uo')
            ->where('o.expiration > :now')
            ->andWhere('o.closed = false')
            ->setParameter(':now', $now)
            ->orderBy('o.expiration')
        ;

        // It would be better to use HAVING COUNT expression, but I'm not sure it's really possible in that case.

        $out = array();
        foreach($q->getQuery()->execute() as $order) {
            if($order->getAvailableOrders() > 0) $out[] = $order;
        }

        return $out;
        
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
