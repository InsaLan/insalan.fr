<?php

namespace InsaLan\UserBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class UserAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('email', EmailType::class)
            ->add('username')
            ->add('firstname', null, array("required" => false))
            ->add('lastname', null, array("required" => false))
            ->add('phoneNumber', null, array("required" => false))
            ->add('roles')
            ->add('enabled')
            ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('username')
            ->add('firstname')
            ->add('lastname')
            ->add('phoneNumber')
            ->add('email')
            ->add('roles')
            ->add('enabled')
            ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('username')
            ->add('email')
            ->add('lastLogin')
            ->add('enabled')
            ->add('firstname')
            ->add('lastname')
            ->add('phoneNumber')
            ;
    }

    public function prePersist($object)
        {
            parent::prePersist($object);

        }

    public function preUpdate($object)
    {
        parent::preUpdate($object);
        $this->updateUser($object);
    }

    //update password
    public function updateUser(\InsaLan\UserBundle\Entity\User $u) {
      if ($u->getPlainPassword()) {
        $u->setPlainPassword($u->getPlainPassword());
      }

      $um = $this->getConfigurationPool()->getContainer()->get('fos_user.user_manager');
      $um->updateUser($u, false);
    }

}
