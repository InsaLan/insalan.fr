<?php

namespace InsaLan\TournamentBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ActionType;

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
            ->add('statsType', 'choice', array(
                'choices' => array(
                    Group::STATS_WINLOST => 'Victoires/Défaites',
                    Group::STATS_SCORE => 'Somme des scores'
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
        $this->autoManageMatches($group);
    }

    public function prePersist($group)
    {
        $this->autoManageMatches($group);
    }

    private function autoManageMatches($group)
    {

        $em = $this->getConfigurationPool()->getContainer()->get('Doctrine')->getManager();

        if ($group->getStatsType() == Group::STATS_WINLOST) {
            // Clean up deprecated matches

            foreach($group->getMatches()->toArray() as $match) {

                foreach($match->getParticipants() as $p) {
                    if (!$group->hasParticipant($p)) {

                        $group->removeMatch($match);
                        $em->remove($match);
    
                        break;
                    }
                }
            }

            // Create missing matches

            $participants = $group->getParticipants()->getValues();

            for($i = 0; $i < count($participants); $i++)
            {
                for($j = $i+1; $j < count($participants); $j++)
                {

                    $a = $participants[$i];
                    $b = $participants[$j];

                    if(!$group->getMatchBetween($a, $b)) {
                        $match = new Match();
                        $match->setPart1($a);
                        $match->setPart2($b);
                        $match->setState(Match::STATE_UPCOMING);
                        $match->setGroup($group);
                        $group->addMatch($match);
                        $em->persist($match);
                    }

                }
            }
        }
        else {
            // assume STATS_SCORE groups only have 1 RoyalMatch with every participants => battle royale tournaments

            // create match if missing
            if ($group->getMatches()->count() == 0) {
                $m = new RoyalMatch();
                $m->setState(Match::STATE_UPCOMING);
                $m->setGroup($group);
                $group->addMatch($m);
            }

            $match = $group->getMatches()->first();

            // set match participants to all group participants
            foreach($match->getParticipants() as $p) {
                if (!$group->hasParticipant($p)) {
                    $match->removeParticipant($p);
                }
            }

            foreach($group->getParticipants() as $p) {
                if (!$match->hasParticipant($p)) {
                    $match->addParticipant($p);
                }
            }

            $em->persist($match);
        }

    }
}
