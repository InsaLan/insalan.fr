<?php

namespace InsaLan\TournamentBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

use InsaLan\TournamentBundle\Entity\Match;

class GroupAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
            ->add('stage')
            ->add('participants')
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
            ->add('_action','actions',array('actions'  => array('edit' => array(),'show' => array())));
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

        // Clean up deprecated matches
        
        foreach($group->getMatches()->toArray() as $match) {

            if(!$group->hasParticipant($match->getPart1()) ||
               !$group->hasParticipant($match->getPart2())) {

                $group->removeMatch($match);
                $em->remove($match);

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
}
