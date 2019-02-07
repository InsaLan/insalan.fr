<?php

namespace InsaLan\TournamentBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

use InsaLan\TournamentBundle\Entity\Match;

class RoundAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('match')
            ->add('scores', 'sonata_type_collection', array(
                'by_reference' => true,
                'label' => "Scores",
                'type_options' => array('delete' => false),
                'cascade_validation' => true,
                'btn_add' => false,
                'required' => true
            ), array(
                'edit' => 'inline',
                'inline' => 'table'
            ))
            ->add('replayFile', 'file', array('required' => false, 'label' => "Fichier de replay"))
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {   
        $showMapper
            ->add("Détails", null, array("template" => "InsaLanTournamentBundle:Admin:admin_extra_infos.html.twig"))
            ->add('match',   null, array('route' => array('name' => 'show')))
            ->add('scores',  null, array('label' => "Scores"))
            ->add('fullReplay', "string", array('label' => "Replay"))
        ;
    }


    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('match')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('match', null, array('label' => "Match"))
            ->add('match.part1', null, array('label' => "Participant 1"))
            ->add('match.part2', null, array('label' => "Participant 2"))
            ->add('scores',      null, array('label' => "Scores"))
            ->add('_action','actions',array('actions'  => array('edit' => array())));
        ;
    }

    public function removeOldParticipants($round)
    {
        $em = $this->getConfigurationPool()->getContainer()->get('Doctrine')->getManager();

        foreach ($round->getScores() as $score) {
            if (!$round->getMatch()->getParticipants()->contains($score->getParticipant())) {
                $em->remove($score);
            }
        }

        $em->flush();
    }

    public function addNewParticipants($round)
    {
        $em = $this->getConfigurationPool()->getContainer()->get('Doctrine')->getManager();

        foreach ($round->getMatch()->getParticipants() as $p) {
            if (!$round->hasScore($p)) {
                $round->setScore($p, 0);
            }
        }
        
        $em->flush();
    }

    public function preUpdate($round)
    {
        $this->removeOldParticipants($round);
    }

    public function postPersist($round)
    {
        $this->addNewParticipants($round);
    }

    public function postUpdate($round)
    {
        $this->addNewParticipants($round);

        $match = $round->getMatch();
        if($match->getState() === Match::STATE_FINISHED && $match->getKoMatch())
        {
            $em = $this->getConfigurationPool()->getContainer()->get('Doctrine')->getManager();
            $repository = $em->getRepository('InsaLanTournamentBundle:KnockoutMatch');
            $repository->propagateVictory($match->getKoMatch());
            $em->flush();
        }
    }


}
