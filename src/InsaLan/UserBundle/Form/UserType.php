<?php

namespace InsaLan\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', null, array('required' => true, 'label' => 'Prénom*'))
            ->add('lastname', null, array('required' => true, 'label' => 'Nom*'))
            ->add('phoneNumber', null, array('required' => true, 'label' => 'Portable*'))
            ->add('birthdate', BirthdayType::class, array('required' => true, 'label' => 'Date de naissance*'))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'InsaLan\UserBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'insalan_userbundle_user';
    }
}
