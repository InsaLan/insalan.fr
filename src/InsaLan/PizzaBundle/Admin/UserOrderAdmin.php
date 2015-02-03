<?php

namespace InsaLan\PizzaBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class UserOrderAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('user', null, array("label" => "Utilisateur", "required" => false))
            ->add('usernameCanonical', null, array("label" => "Nom d'utilisateur", "required" => false))
            ->add('fullnameCanonical', null, array("label" => "Prénom NOM", "required" => false))
            ->add('pizza', null, array("label" => "Pizza"))
            ->add('paymentDone', null, array("label" => "Paiement effectué", "required" => false))
            ->add('delivered', null, array("label" => "Livrée", "required" => false))
            ->add('type', 'choice', array(
                'choices' => array(
                    0 => 'Manuel',
                    1 => 'Paypal'
                ),
                'label' => "Type de paiement",
                'required' => true
            ))
            ->add('price', 'choice', array(
                'choices' => array(
                    0 => 'Plein tarif',
                    1 => 'Tarif staff',
                    2 => 'Offert'
                ),
                'label' => "Tarif",
                'required' => true
            ))
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('user', null, array("label" => "Utilisateur"))
            ->add('usernameCanonical', null, array("label" => "Nom d'utilisateur"))
            ->add('fullnameCanonical', null, array("label" => "Prénom NOM"))
            ->add('pizza', null, array("label" => "Pizza"))
            ->add('paymentDone', null, array("label" => "Paiement effectué"))
            ->add('delivered', null, array("label" => "Pizza livrée"))
            ->add('type', null, array('label' => "Type"))
            ->add('price', null, array('label' => "Tarif"))
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id', null, array("label" => "id"))
            ->add('username', null, array("label" => "Pseudo"))
            ->add('pizza', null, array("label" => "Pizza"))
            ->add('paymentDone', null, array("label" => "Paiement"))
            ->add('delivered', null, array("label" => "Livraison"))
            ->add('type', null, array('label' => "Type"))
            ->add('price', null, array('label' => "Tarif"))
        ;
    }
}
