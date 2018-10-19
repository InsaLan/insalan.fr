<?php

namespace InsaLan\TournamentBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ActionType;
use Symfony\Component\Form\Extension\Core\Type\StringType;

use InsaLan\TournamentBundle\Entity\Match;

class RoundAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('match')
            ->add('score1', null, array('label' => "Score 1"))
            ->add('score2', null, array('label' => "Score 2"))
            ->add('replayFile', FileType::class, array('required' => false, 'label' => "Fichier de replay"))
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add("Détails", null, array("template" => "InsaLanTournamentBundle:Admin:admin_extra_infos.html.twig"))
            ->add('match',   null, array('route' => array('name' => 'show')))
            ->add('score1',  null, array('label' => "Score 1"))
            ->add('score2',  null, array('label' => "Score 2"))
            ->add('fullReplay', StringType::class, array('label' => "Replay"))
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
            ->add('match.part1', null, array('label' => "Participant 1"))
            ->add('match.part2', null, array('label' => "Participant 2"))
            ->add('score1',      null, array('label' => "Score 1"))
            ->add('score2',      null, array('label' => "Score 2"))
            ->add('_action', ActionType::class,array('actions'  => array('edit' => array())));
        ;
    }

    public function postUpdate($round)
    {

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
