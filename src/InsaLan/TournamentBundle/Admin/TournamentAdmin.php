<?php

namespace InsaLan\TournamentBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class TournamentAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
            ->add('shortName')
            ->add('description')
            ->add('rules')
            ->add('playerInfos')
            ->add('registrationOpen')
            ->add('registrationClose')
            ->add('tournamentOpen')
            ->add('tournamentClose')
            ->add('registrationLimit')
            ->add('webPrice')
            ->add('currency')
            ->add('onlineIncreaseInPrice')
            ->add('onSitePrice')
            ->add('teamMaxPlayer')
            ->add('teamMinPlayer')
            ->add('maxManager')
            ->add('locked', null, array('required'=>false))
            ->add('placement', null, array('required' => false))
            ->add('participantType', 'choice', array(
                'choices' => array(
                    'team' => 'Par équipe',
                    'player' => 'Joueur seul'
                ),
                'required' => true
            ))
            ->add('type', 'choice', array(
                'choices'   => array(
                    'lol' => 'League of Legends',
                    'csgo' => 'Counter Strike Global Offensive',
                    'hs' => 'HearthStone',
                    'dota2' => 'Dota 2',
                    'sc2' => 'StarCraft 2',
                    'ow' => 'Overwatch',
                    'sfv' => 'Street Fighter V',
                    'manual' => 'Autre/Manuel'),
                'required'  => true))
            ->add('file', 'file', array('required' => false))
			->add('loginType','choice', array(
				'choices' => array(
					'other' => 'Autre',
					'Steam' => 'Steam',
					'battlenet' => 'BattleNet'					
				),
				'required' => true
			))
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('type')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('registrationOpen')
            ->add('registrationClose')
            ->add('registrationLimit')
            ->add('participantType')
            ->add('type')
        ;
    }
    
    public function prePersist($e) {
        $e->upload();
    }
    
    public function preUpdate($e) {
        $this->prePersist($e);
    }
}
