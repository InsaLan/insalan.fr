<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\StringType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use App\Entity\TournamentMatch as Match;

class TournamentRoyalMatchAdmin extends AbstractAdmin
{

    protected $stateDef = array(
        'En attente' => Match::STATE_UPCOMING,
        'En cours' => Match::STATE_ONGOING,
        'Terminé' => Match::STATE_FINISHED
   );

    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('participants', null,
                array('class' => 'App\Entity\Participant'))
            ->add('group', EntityType::class,
                array('class' => 'App\Entity\Group'))
            ->add('state', ChoiceType::class, array(
                'choices'   => $this->stateDef,
                'required'  => true,
                'label'     => "Statut"))
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add("Tournois", StringType::class, array("template" => "InsaLanTournamentBundle:Admin:admin_extra_infos.html.twig"))
            ->add('participants', null, array('label' => "Participants"))
            ->add('group', null, array('label' => "Poule"))
            ->add('koMatch.knockout', null, array('label' => "Arbre"))
            ->add('state', ChoiceType::class, array(
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
            ->add('state', 'doctrine_orm_string', array(), ChoiceType::class, array('choices' => $this->stateDef))
            ->add('participants', null, array('label' => "Participants"))
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('participants', null, array('label' => "Participants"))
            ->add('extraInfos', null, array('label' => "Conteneur"))
            ->add('_action', ActionType::class,
                array('actions'  => array('show' => array(),
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
