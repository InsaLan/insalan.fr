<?php

namespace InsaLan\TournamentBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class KnockoutAdmin extends Admin
{

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('tournament')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->addIdentifier('tournament')
        ;
    }

    public function postPersist($ko)
    {   

        $em = $this->getConfigurationPool()->getContainer()->get('Doctrine')->getManager();
        $repository = $em->getRepository('InsaLanTournamentBundle:KnockoutMatch');

        $repository->generateMatches($ko, $ko->getSize());

    }
}
