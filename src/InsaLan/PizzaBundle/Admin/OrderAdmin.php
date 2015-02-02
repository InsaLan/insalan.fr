<?php

namespace InsaLan\PizzaBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class OrderAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('expiration', null, array('label' => "Horaire limite"))
            ->add('delivery', null, array('label' => "Horaire de livraison"))
            ->add('capacity', null, array('label' => "Nombre de pizzas max"))
            ->add('closed', null, array('required' => false, 'label' => "Verrouillé"));
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('delivery', null, array('label' => "Horaire de livraison"))
            ->addIdentifier('capacity', null, array('label' => "Capacité"))
            ->addIdentifier('closed', null, array('required' => false, 'label' => "Verrouillé"));
        ;
    }
}
