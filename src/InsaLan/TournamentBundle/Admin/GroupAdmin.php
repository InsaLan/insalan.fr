<?php

namespace InsaLan\TournamentBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ActionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use InsaLan\TournamentBundle\Entity\Match;
use InsaLan\TournamentBundle\Entity\RoyalMatch;
use InsaLan\TournamentBundle\Entity\Group;
use InsaLan\TournamentBundle\Entity\Participant;
use InsaLan\TournamentBundle\Entity\ParticipantRepository;

class GroupAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
            ->add('stage')
            // Display only validated participants
            ->add('participants', null, array('query_builder' => function(ParticipantRepository $er) {
                                        return $er->createQueryBuilder('e')
                                                  ->leftJoin('e.manager', 'ma')
                                                  ->addSelect('ma')
                                                  ->where('e.validated = :status')
                                                  ->setParameter('status', Participant::STATUS_VALIDATED);
                                    })
            )
            ->add('statsType', ChoiceType::class, array(
                'choices' => array(
                    'Victoires/Défaites' => Group::STATS_WINLOST,
                    'Somme des scores' => Group::STATS_SCORE
                ),
                'required' => true
            ))
            // DISABLED SEE BELOW ->add('matches', 'sonata_type_collection', array(), array('edit' => 'inline', 'inline' => 'table'))
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('stage')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('stage')
            ->add("Participants", null, array("template" => "InsaLanTournamentBundle:Admin:admin_extra_infos.html.twig"))
            ->add('_action', ActionType::class,array('actions'  => array('edit' => array(),'show' => array())));
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
            ->add('stage')
            ->add('participants')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);

        $query
            ->addSelect('s')
            ->addSelect('t')
            ->leftJoin($query->getRootAlias().'.stage', 's')
            ->leftJoin('s.tournament', 't')
        ;

        return $query;
    }

    public function preUpdate($group)
    {
        $em = $this->getConfigurationPool()->getContainer()->get('doctrine')->getManager();
        $em->getRepository('InsaLanTournamentBundle:Group')->autoManageMatches($group);
    }

    public function prePersist($group)
    {
        $em = $this->getConfigurationPool()->getContainer()->get('doctrine')->getManager();
        $em->getRepository('InsaLanTournamentBundle:Group')->autoManageMatches($group);
    }

}
