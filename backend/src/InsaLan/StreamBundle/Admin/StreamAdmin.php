<?php

namespace InsaLan\StreamBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class StreamAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('streamer', null, array("label" => "Streamer"))
            ->add('streamLink', null, array("label" => "Lien du stream"))
            ->add('tournament', null, array("label" => "Tournoi"))
            ->add('official', null, array("label" => "Officiel"))
            ->add('display', null, array("label" => "Visible"))
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
        ->add('streamer', null, array("label" => "Streamer"))
        ->add('streamLink', null, array("label" => "Lien du stream"))
        ->add('tournament', null, array("label" => "Tournoi"))
        ->add('official', null, array("label" => "Officiel"))
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('streamer', null, array("label" => "Streamer"))
            ->addIdentifier('tournament', null, array("label" => "Tournoi"))
            ->add('official', null, array("label" => "Officiel"))
            ->add('display', null, array("label" => "Visible"))
        ;
    }
}
