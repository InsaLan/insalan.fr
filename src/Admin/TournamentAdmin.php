<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

use App\Service\LoginPlatform;

class TournamentAdmin extends AbstractAdmin
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
            ->add('participantType', ChoiceType::class, array(
                'choices' => array(
                    'Par équipe' => 'team',
                    'Joueur seul' => 'player'
                ),
                'required' => true
             ))
             ->add('type', ChoiceType::class, array(
                 'choices'   => array(
                     'League of Legends' => 'lol',
                     'Counter Strike Global Offensive' => 'csgo',
                     'HearthStone' => 'hs',
                     'Dota 2' => 'dota2',
                     'StarCraft 2' => 'sc2',
                     'Overwatch' => 'ow',
                     'Street Fighter V' => 'sfv',
                     'Dragon Ball FighterZ' => 'dbfz',
                     'Fortnite Battle Royale' => 'fbr',
                     'Autre/Manuel' => 'manual'),
                'required'  => true))
            ->add('file', FileType::class, array('required' => false))
      			->add('loginType', ChoiceType::class, array(
      				'choices' => array(
      					  'Autre' => LoginPlatform::PLATFORM_OTHER,
      					  'Steam' => LoginPlatform::PLATFORM_STEAM,
      					  'BattleNet' => LoginPlatform::PLATFORM_BATTLENET,
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
