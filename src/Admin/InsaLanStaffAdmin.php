<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class InsaLanStaffAdmin extends AbstractAdmin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('role', null, array("label" => "Role"))
            ->add('lastName', null, array("label" => "Nom"))
            ->add('firstName', null, array("label" => "Prénom"))
            ->add('email', null, array("label" => "Email"))
            ->add('phone', null, array("label" => "Numéro de téléphone"))
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('role', null, array("label" => "Role"))
            ->add('lastName', null, array("label" => "Nom"))
            ->add('firstName', null, array("label" => "Prénom"))
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('role', null, array("label" => "Role"))
            ->add('lastName', null, array("label" => "Nom"))
            ->add('firstName', null, array("label" => "Prénom"))
        ;
    }
}
