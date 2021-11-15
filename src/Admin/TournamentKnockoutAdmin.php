<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class TournamentKnockoutAdmin extends AbstractAdmin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
        ->add('name', TextType::class,
            array('disabled' => true))
        ;
    }


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

    /*public function postPersist($ko)
    {

        $em = $this->getConfigurationPool()->getContainer()->get('doctrine')->getManager();
        $repository = $em->getRepository('InsaLanTournamentBundle:KnockoutMatch');

        $repository->generateMatches($ko, $ko->getSize());

    }*/
}
