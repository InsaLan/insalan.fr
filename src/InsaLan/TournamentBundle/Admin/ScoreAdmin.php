<?php

namespace InsaLan\TournamentBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

use InsaLan\TournamentBundle\Entity\Match;

class ScoreAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('participant', 'entity',
                array('disabled' => true, 'class' => 'InsaLan\TournamentBundle\Entity\Participant'))
            ->add('score')
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('participant', 'entity',
                array('disabled' => true, 'class' => 'InsaLan\TournamentBundle\Entity\Participant'))
            ->add('score')
        ;
    }

}
