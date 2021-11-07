<?php

namespace InsaLan\TournamentBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

use InsaLan\UserBundle\Entity\User;

class TeamAdmin extends AbstractAdmin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
            ->add('tournament')
            ->add('captain')
            ->add('manager')
            ->add('validated', ChoiceType::class, array(
                'choices_as_values' => true,
                'required' => true,
                'choices' => array(
                    'Équipe incomplète' => 0,
                    'Dans la liste d\'attente' => 1,
                    'Validée' => 2
                )
            ))
            ->add('plainPassword', RepeatedType::class, array(
                'required' => false,
                'type' => PasswordType::class,
                'first_options' => array('label' => 'Mot de passe'),
                'second_options' => array('label' => 'Confirmation de mot de passe'),
            ))
            ->add('placement')
            ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('tournament')
            ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('tournament.name')
            ->add('captain.name')
            ->add('validated')
            ->add('validationDate')
            ->add('placement')
        ;
    }
    public function prePersist($e)
    {
        $this->hashPassword($e);
    }

    public function preUpdate($e)
    {
        $this->hashPassword($e);
    }

    protected function hashPassword($e)
    {
        if ($e->getPlainPassword() !== null && $e->getPlainPassword() !== "") {
            $container = $this->getConfigurationPool()->getContainer();
            $factory = $container->get('security.encoder_factory');
            $encoder = $factory->getEncoder(new User());
            $e->setPassword($encoder->encodePassword($e->getPlainPassword(), $e->getPasswordSalt()));
        }
    }
}
