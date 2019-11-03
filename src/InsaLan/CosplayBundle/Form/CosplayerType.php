<?php

namespace InsaLan\CosplayBundle\Form;

use InsaLan\CosplayBundle\Entity\Cosplayer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class CosplayerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', null, array('label' => "Prénom"))
            ->add('lastname', null, array('label' => "Nom"))
            ->add('pseudo', null, array('label' => "Pseudo"))
            ->add('usePseudo', CheckboxType::class, array('label' => "Utiliser le pseudo"))
            ->add('adult', CheckboxType::class, array('label' => "Adulte"))
            ->add('email', EmailType::class, array('label' => "Email"))
            ->add('phone', null, array('label' => "Téléphone"))
            ->add('postalCode', IntegerType::class, array('label' => "Code postal"))
            ->add('characterCosplayed', null, array('label' => "Personnage cosplayé"))
            ->add('origin', null, array('label' => "Origine (Manga/Jeu Vidéo/Film...)"))
            ->add('picturePath', FileType::class, [
                'label' => 'Image de référence',
                'required' => false
            ])
            ->add('pictureRightPath', FileType::class, [
                'label' => "Droit à l'image",
                'required' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Format PDF uniquement',
                    ])
                ]
            ])
            ->add('parentalConsentPath', FileType::class, [
                'label' => 'Autorisation parentale',
                'required' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Format PDF uniquement',
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Cosplayer::class,
        ]);
    }
}