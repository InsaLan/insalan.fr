<?php

namespace InsaLan\ArchivesBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\FileType;

use InsaLan\ArchivesBundle\Entity\Picture;

class PictureAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
            ->add('date')
            ->add('file', FileType::class, [
                'required' => false
            ])
            ->add('album')
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('path')
            ->add('date')
            ->add('album')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('path')
            ->add('date')
            ->add('album')
        ;
    }

        public function prePersist($image)
        {
            $this->manageFileUpload($image);
        }

        public function preUpdate($image)
        {
            $this->manageFileUpload($image);
        }

        private function manageFileUpload($image)
        {
            if ($image->getFile()) {
                $image->upload();
            }
        }

}
