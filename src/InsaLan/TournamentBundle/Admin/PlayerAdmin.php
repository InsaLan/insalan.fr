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
            ->add('name')
            ->add('user')
            ->add('team')
            ->add('lolIdValidated')
            ->add('lolPicture')
            ->add('lolId')
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('team')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->addIdentifier('user.email')
            ->addIdentifier('team.name')
        ;
    }

    public function postPersist($e)
    {
        $e->onPostPersist();
    }

    public function postUpdate($e)
    {
        $e->onPostUpdate();
    }

    public function preRemove($e)
    {
        $e->onPreRemove();
    }
}
