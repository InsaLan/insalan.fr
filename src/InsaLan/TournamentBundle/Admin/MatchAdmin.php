<?php

namespace InsaLan\TournamentBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

use InsaLan\TournamentBundle\Entity\Match;

class MatchAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            /*->add('part1', 'entity',
                array('read_only' => true, 'disabled' => true, 'class' => 'InsaLan\TournamentBundle\Entity\Participant'))
            ->add('part2', 'entity',
                array('read_only' => true, 'disabled' => true, 'class' => 'InsaLan\TournamentBundle\Entity\Participant'))
            ->add('group', 'entity',
                array('read_only' => true, 'disabled' => true, 'class' => 'InsaLan\TournamentBundle\Entity\Group'))*/
            ->add('state', 'choice', array(
                'choices'   => array(
                    Match::STATE_UPCOMING => 'En attente',
                    Match::STATE_ONGOING  => 'En cours',
                    Match::STATE_FINISHED => 'Terminé'
                    ),
                'required'  => true))
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add("Tournois", "string", array("template" => "InsaLanTournamentBundle:Admin:admin_extra_infos.html.twig"))
            ->add('part1', null, array('label' => "Participant 1"))
            ->add('part2', null, array('label' => "Participant 2"))
            ->add('group', null, array('label' => "Groupe"))
            ->add('state', 'choice', array(
                'choices'   => array(
                    Match::STATE_UPCOMING => 'En attente',
                    Match::STATE_ONGOING  => 'En cours',
                    Match::STATE_FINISHED => 'Terminé'
                ),
                'label'     => "Statut"))
            ->add('rounds', null, array('route' => array('name' => 'show')));
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('group')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('part1', null, array('label' => "Participant 1"))
            ->add('part2', null, array('label' => "Participant 2"))
            ->add('group.name', null, array('label' => "Groupe"))
            ->add('_action','actions',array('actions'  => array('view' => array(), 'edit' => array())));
        ;
    }


}
