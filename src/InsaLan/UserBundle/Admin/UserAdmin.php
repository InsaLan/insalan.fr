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
            ->add('plainPassword', RepeatedType::class, array(
                'required' => false,
                'type' => PasswordType::class,
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array('label' => 'form.password'),
                'second_options' => array('label' => 'form.password_confirmation'),
                'invalid_message' => 'fos_user.password.mismatch',
            ))
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

}
