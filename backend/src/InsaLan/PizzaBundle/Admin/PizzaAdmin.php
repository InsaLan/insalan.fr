<?php

namespace InsaLan\PizzaBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class PizzaAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', null, array("label" => "Nom"))
            ->add('price', null, array("label" => "Prix"))
            ->add('veggie', null, array("label" => "Végétarienne"))
            ->add('description', null, array("label" => "Description", "required" => false))
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, array("label" => "Nom"))
            ->add('price', null, array("label" => "Prix"))
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name', null, array("label" => "Nom"))
            ->addIdentifier('price', null, array("label" => "Prix"))
            ->add('veggie', null, array("label" => "Végétarienne"))
        ;
    }
}
