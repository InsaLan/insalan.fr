<?php

namespace InsaLan\PizzaBundle\Entity;

use Doctrine\ORM\EntityRepository;

class OrderRepository extends EntityRepository
{   

    public function getOneById($id) {

        $q = $this->createQueryBuilder('o')
            ->leftJoin('o.orders', 'uo')
            ->leftJoin('uo.user', 'u')
            ->leftJoin('uo.pizza', 'p')
            ->addSelect('p')
            ->addSelect('uo')
            ->addSelect('u')
            ->where('o.id = :id')
            ->orderBy('uo.usernameCanonical', 'asc')
            ->setParameter('id', $id)
        ;

        return $q->getQuery()->getSingleResult();

    }

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
            ->addSelect('uo')
            ->addSelect('u')
            //->where('uo.paymentDone = true')
            ->orderBy('o.expiration', 'asc')
        ;

        return $q->getQuery()->execute();
    }
}
