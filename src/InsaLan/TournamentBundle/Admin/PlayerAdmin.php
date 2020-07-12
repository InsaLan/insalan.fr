<?php

namespace InsaLan\TournamentBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PlayerAdmin extends AbstractAdmin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('user')
            ->add('team')
            ->add('manager')
            ->add('gameName')
            ->add('gameValidated', null, array('required'=>false))
            ->add('gameAvatar')
            ->add('gameId')
            ->add('tournament')
            ->add('paymentDone', null, array('required'=>false))
            ->add('validated', ChoiceType::class, array(
                'choices_as_values' => true,
                'required' => true,
                'choices' => array(
                    'Non payé' => 0,
                    'Dans la liste d\'attente' => 1,
                    'Validée' => 2
                )
            ))
            ->add('arrived', null, array('required'=>false))
            ->add('placement')
            ->add('pendingRegistrable', null, array('required' => false))

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
            ->add('arrived')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('user.username')
            ->addIdentifier('gameName')
            ->add('tournament')
            ->add('pendingRegistrable')
            ->add('user.email')
            ->add('team.name')
            ->add('paymentDone')
            ->add('validationDate')
            ->add('placement')
            ->add('arrived')
        ;
    }

}
