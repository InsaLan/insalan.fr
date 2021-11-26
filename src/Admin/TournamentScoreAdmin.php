<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

use App\Entity\TournamentMatch as Match;

class TournamentScoreAdmin extends AbstractAdmin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('participant', 'entity',
                array('disabled' => true, 'class' => 'App\Entity\Participant'))
            ->add('score')
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('participant', 'entity',
                array('disabled' => true, 'class' => 'App\Entity\Participant'))
            ->add('score')
        ;
    }

}
