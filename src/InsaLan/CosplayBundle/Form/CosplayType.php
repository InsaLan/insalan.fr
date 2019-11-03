<?php

namespace InsaLan\CosplayBundle\Form;

use InsaLan\CosplayBundle\Entity\Cosplay;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class CosplayType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('team', CheckboxType::class, array('label' => "Groupe"))
            ->add('name', null, array(
                'label' => "Nom du groupe",
                'required' => false
            ))
            ->add('launch', ChoiceType::class, array(
                'label' => "Doit-on lancer la prestation avant ou après votre entrée sur scène ?",
                'choices'  => [
                    'Avant' => 'before',
                    'Après' => 'after',
                ]
            ))
            ->add('setup', null, array('label' => "Installation particulière pour la scène  ?"))
            ->add('details', null, array(
                'label' => "Précisions éventuelles",
                'required' => false
            ))
            ->add('soundtrack', FileType::class, [
                'label' => 'Bande-son de la prestation',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Cosplay::class,
        ]);
    }
}