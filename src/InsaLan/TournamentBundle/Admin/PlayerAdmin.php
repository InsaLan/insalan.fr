<?php

namespace InsaLan\TournamentBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class PlayerAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('user')
            ->add('team')
            ->add('gameName')
            ->add('gameValidated', null, array('required'=>false))
            ->add('gameAvatar')
            ->add('gameId')
            ->add('tournament')
            ->add('paymentDone')
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('gameName')
            ->add('user.username')
            ->add('user.email')
            ->add('team')
            ->add('tournament')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('user.username')
            ->addIdentifier('gameName')
            ->add('tournament')
            ->add('user.email')
            ->add('team.name')
            ->add('paymentDone')
        ;
    }

}
