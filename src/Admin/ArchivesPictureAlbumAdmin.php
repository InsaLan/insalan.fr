<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use App\Entity\PictureAlbum;

class ArchivesPictureAlbumAdmin extends AbstractAdmin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', null, array ("label" => "Nom"))
            ->add('url', null, array ("label" => "Lien URL"))
            ->add('edition', null, array('class' => 'App\Entity\ArchivesEdition'))
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, array ("label" => "Nom"))
            ->add('url', null, array ("label" => "Lien URL"))
            ->add('edition', null, array('class' => 'App\Entity\ArchivesEdition'))
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name', null, array ("label" => "Nom"))
            ->add('url', null, array ("label" => "Lien URL"))
            ->add('edition', null, array('class' => 'App\Entity\ArchivesEdition'))
        ;
    }
}
