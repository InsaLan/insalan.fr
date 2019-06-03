<?php

namespace InsaLan\NewsBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

use Symfony\Component\Form\Extension\Core\Type\FileType;

final class PictureAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('fileName', null, array('label' => "Nom du fichier"))
            ->add('file', FileType::class, [
                'label' => "Fichier",
                'required' => false
            ])
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('fileName', null, array('label' => "Nom du fichier"))
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('fileName', null, array('label' => "Nom du fichier"))
        ;
    }

    public function prePersist($picture)
    {
        $this->manageFileUpload($picture);
    }

    public function preUpdate($picture)
    {
        $this->manageFileUpload($picture);
    }

    private function manageFileUpload($picture)
    {
        if ($picture->getFile()) {
            $picture->upload();
            $picture->refreshUpdated();
        }
    }
}
