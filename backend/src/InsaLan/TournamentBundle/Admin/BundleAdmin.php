<?php

namespace InsaLan\TournamentBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class BundleAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
            ->add('description')
            ->add('tournaments')
            ->add('registrationOpen')
            ->add('registrationClose')
            ->add('registrationLimit')
            ->add('webPrice')
            ->add('currency')
            ->add('onlineIncreaseInPrice')
            ->add('onSitePrice')
            ->add('locked', null, array('required'=>false))
            ->add('file', FileType::class, array('required' => false))
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('tournaments')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add("Tournois", null, array("template" => "InsaLanTournamentBundle:Admin:admin_bundle_tournaments.html.twig"))
            ->add('registrationOpen')
            ->add('registrationClose')
            ->add('registrationLimit')
        ;
    }

    public function validate(ErrorElement $errorElement, $e)
    {
        foreach ($e->getTournaments() as $t) {
            if ($t->getParticipantType() !== "player") {
                $errorElement->with('tournaments')->addViolation('Seuls les tournois solo sont supportés dans les Bundles !');
                break;
            }
        }
    }

    public function prePersist($e) {
        $e->upload();
    }

    public function preUpdate($e) {
        $this->prePersist($e);
    }
}
