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

use App\Entity\TournamentMatch;

class TournamentMatchAdmin extends AbstractAdmin
{
    // Patch to switch from Symfony2 to Symfony3. We have to switch keys and values in arrays for choices.
    protected $stateDef = array(
                             'En attente' => TournamentMatch::STATE_UPCOMING,
                             'En cours' => TournamentMatch::STATE_ONGOING,
                             'Terminé' => TournamentMatch::STATE_FINISHED
                        );

    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            /*->add('part1', 'entity',
                array('read_only' => true, 'disabled' => true, 'class' => 'App\Entity\Participant'))
            ->add('part2', 'entity',
                array('read_only' => true, 'disabled' => true, 'class' => 'App\Entity\Participant'))
            ->add('group', 'entity',
                array('read_only' => true, 'disabled' => true, 'class' => 'App\Entity\Group'))*/
            ->add('state', ChoiceType::class, array(
                'choices'   => $this->stateDef,
                'required'  => true))
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add("Tournois", StringType::class, array("template" => "Admin/tournamentAdmin_extra_infos.html.twig"))
            ->add('part1', null, array('label' => "Participant 1"))
            ->add('part2', null, array('label' => "Participant 2"))
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
            ->add('_action', ActionType::class,
                array('actions'  => array('show' => array(),
                      'edit' => array(),
                      'createRound' => array(
                        'template' => 'Admin/tournamentList__action_create_round.html.twig'
                      ))))
        ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('createRound', $this->getRouterIdParameter().'/createRound');
    }

    public function postUpdate($match)
    {
        if($match->getState() === TournamentMatch::STATE_FINISHED && $match->getKoMatch())
        {
            $em = $this->getConfigurationPool()->getContainer()->get('doctrine')->getManager();
            $repository = $em->getRepository('App\Entity\TournamentKnockoutMatch');
            $repository->propagateVictory($match->getKoMatch());

            $ko = $match->getKoMatch()->getKnockout();

            if($ko->getDoubleElimination()) {
                // deal with it.
                $repository->propagateFromNode(
                    $repository
                    ->getRoot($ko)
                    ->getChildren()
                    ->get(1)); // only update the hard part of the tree
            }

            $em->flush();
        }
    }

}
