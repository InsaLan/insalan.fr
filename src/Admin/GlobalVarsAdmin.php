<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

final class GlobalVarsAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('id')
            ->add('staffNumber')
            ->add('number')
            ->add('lettersNumber')
            ->add('romanNumber')
            ->add('playersNumber')
            ->add('openingDate')
            ->add('openingHour')
            ->add('closingDate')
            ->add('closingHour')
            ->add('price')
            ->add('webPrice')
            ->add('campanilePrice')
            ->add('cosplayEdition')
            ->add('cosplayName')
            ->add('cosplayDate')
            ->add('cosplayEndRegistration')
            ->add('fullDates')
            ->add('payCheckAddress')
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('id')
            ->add('staffNumber')
            ->add('number')
            ->add('lettersNumber')
            ->add('romanNumber')
            ->add('playersNumber')
            ->add('openingDate')
            ->add('openingHour')
            ->add('closingDate')
            ->add('closingHour')
            ->add('price')
            ->add('webPrice')
            ->add('campanilePrice')
            ->add('cosplayEdition')
            ->add('cosplayName')
            ->add('cosplayDate')
            ->add('cosplayEndRegistration')
            ->add('fullDates')
            ->add('payCheckAddress')
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('id')
            ->add('staffNumber')
            ->add('number')
            ->add('lettersNumber')
            ->add('romanNumber')
            ->add('playersNumber')
            ->add('openingDate')
            ->add('openingHour')
            ->add('closingDate')
            ->add('closingHour')
            ->add('price')
            ->add('webPrice')
            ->add('campanilePrice')
            ->add('cosplayEdition')
            ->add('cosplayName')
            ->add('cosplayDate')
            ->add('cosplayEndRegistration')
            ->add('fullDates')
            ->add('payCheckAddress')
            ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('staffNumber')
            ->add('number')
            ->add('lettersNumber')
            ->add('romanNumber')
            ->add('playersNumber')
            ->add('openingDate')
            ->add('openingHour')
            ->add('closingDate')
            ->add('closingHour')
            ->add('price')
            ->add('webPrice')
            ->add('campanilePrice')
            ->add('cosplayEdition')
            ->add('cosplayName')
            ->add('cosplayDate')
            ->add('cosplayEndRegistration')
            ->add('fullDates')
            ->add('payCheckAddress')
            ;
    }
}
