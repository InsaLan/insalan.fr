<?php

namespace InsaLan\InsaLanBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class GlobalVarsAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('globalKey', null, array("label" => "Clé"))
            ->add('globalValue', null, array("label" => "Valeur"))
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('globalKey', null, array("label" => "Clé"))
            ->add('globalValue', null, array("label" => "Valeur"))
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('globalKey', null, array("label" => "Clé"))
            ->add('globalValue', null, array("label" => "Valeur"))
        ;
    }
}
