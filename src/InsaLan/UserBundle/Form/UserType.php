<?php

namespace InsaLan\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
            ->add('birthdate', 'birthday', array('required' => true, 'label' => 'Date de naissance*'))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'InsaLan\UserBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'insalan_userbundle_user';
    }
}
