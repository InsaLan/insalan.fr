<?php

namespace InsaLan\TournamentBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;

use InsaLan\TournamentBundle\Entity\Match;

class MatchAdmin extends Admin
{   

    protected $stateDef = array(
                            Match::STATE_UPCOMING => 'En attente',
                            Match::STATE_ONGOING  => 'En cours',
                            Match::STATE_FINISHED => 'Terminé'
                        );

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
                'choices'   => $this->stateDef,
                'required'  => true))
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add("Tournois", "string", array("template" => "InsaLanTournamentBundle:Admin:admin_extra_infos.html.twig"))
            ->add('part1', null, array('label' => "Participant 1"))
            ->add('part2', null, array('label' => "Participant 2"))
            ->add('group', null, array('label' => "Poule"))
            ->add('koMatch.knockout', null, array('label' => "Arbre"))
            ->add('state', 'choice', array(
                'choices'   => $this->stateDef,
                'label'     => "Statut"))
            ->add('rounds', null, array('route' => array('name' => 'show')));
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('group', null, array('label' => "Poule"))
            ->add('koMatch.knockout', null, array('label' => "Arbre"))
            ->add('state', 'doctrine_orm_string', array(), 'choice', array('choices' => $this->stateDef))
            ->add('part1', null, array('label' => "Participant 1"))
            ->add('part2', null, array('label' => "Participant 2"))
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('part1', null, array('label' => "Participant 1"))
            ->add('part2', null, array('label' => "Participant 2"))
            ->add('extraInfos', null, array('label' => "Conteneur"))
            ->add('_action','actions',
                array('actions'  => array('view' => array(),
                      'edit' => array(),
                      'createRound' => array(
                        'template' => 'InsaLanTournamentBundle:Admin:list__action_create_round.html.twig'
                      ))))
        ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('createRound', $this->getRouterIdParameter().'/createRound');
    }


}
