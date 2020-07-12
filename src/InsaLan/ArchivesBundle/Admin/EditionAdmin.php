<?php

namespace InsaLan\ArchivesBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use InsaLan\ArchivesBundle\Entity\Edition;

class EditionAdmin extends AbstractAdmin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', null, array ("label" => "Nom"))
            ->add('year', null, array ("label" => "Année"))
            ->add('image', null, array ("label" => "Poster"))
            ->add('trailerAvailable', null, array ("label" => "Trailer disponible"))
            ->add('trailerUrl', null, array ("label" => "Lien URL du trailer"))
            ->add('aftermovieUrl', null, array("label" => "Lien URL de l'aftermovie"))
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, array ("label" => "Nom"))
            ->add('year', null, array ("label" => "Année"))
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name', null, array ("label" => "Nom"))
            ->addIdentifier('year', null, array ("label" => "Année"))
            ->add('trailerAvailable', null, array ("label" => "Trailer disponible"))
        ;
    }
}
