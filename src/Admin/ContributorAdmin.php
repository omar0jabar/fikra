<?php

namespace App\Admin;

use App\Repository\RoleRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class ContributorAdmin
 * @package App\Admin
 */
final class ContributorAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'sonata_admin_contributor';
    protected $baseRoutePattern = 'contributor';
    protected $classnameLabel = 'Contributor';
    protected $translationDomain = 'company';
    protected $datagridValues = [
        '_sort_order' => 'DESC',
        '_sort_by' => 'id',
    ];

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('firstName', null, [
                'label' => 'form.firstName.label'
            ])
            ->add('lastName', null, [
                'label' => 'form.lastName.label'
            ])
            ->add('email', null, [
                'label' => 'form.email.label'
            ])
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('firstName', null, [
                'label' => 'form.firstName.label'
            ])
            ->add('lastName', null, [
                'label' => 'form.lastName.label'
            ])
            ->add('email', null, [
                'label' => 'form.email.label'
            ])
            ->add('isAnonymous', null, [
                'label' => 'form.isAnonymous.label'
            ])
            ->add('createdAt', null, [
                'label' => 'form.contributedAt.label'
            ])
            ->add('status', null, [
                'label' => 'form.status.label'
            ])
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        unset($this->listModes['mosaic']);

        $listMapper
            ->addIdentifier('id')
            ->add('firstName', null, [
                'label' => 'form.firstName.label'
            ])
            ->add('lastName', null, [
                'label' => 'form.lastName.label'
            ])
            ->add('email', null, [
                'label' => 'form.email.label'
            ])
            /*->add('isAnonymous', null, [
                'label' => 'form.isAnonymous.labelBo'
            ])*/
            ->add('contributionAmount', null, [
                'label' => 'form.contributionAmount.labelBo',
                'template' => 'bundles/SonataAdmin/CRUD/list_amount.html.twig'
            ])
            ->add('createdAt', null, [
                'label' => 'form.contributionDate.label',
                'template' => 'bundles/SonataAdmin/CRUD/list_datetime.html.twig'
            ])
            ->add('status', null, [
                'label' => 'form.status.label',
                'template' => 'bundles/SonataAdmin/CRUD/list_status.html.twig'
            ])
            ->add('_action', null, [
                'actions' => [
                    'show' => []
                ]
            ]);;
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('firstName', null, [
                'label' => 'form.firstName.label'
            ])
            ->add('lastName', null, [
                'label' => 'form.lastName.label'
            ])
            ->add('email', null, [
                'label' => 'form.email.label'
            ])
            ->add('isAnonymous', null, [
                'label' => 'form.isAnonymous.labelBo'
            ])
            ->add('contributionAmount', null, [
                'label' => 'form.contributionAmount.labelBo',
                'template' => 'bundles/SonataAdmin/CRUD/show_amount.html.twig'
            ])
            ->add('chosenPayment', null, [
                'label' => 'form.chosenPayment.label'
            ])
            ->add('amountDebited', null, [
                'label' => 'form.amountDebited.label',
                'template' => 'bundles/SonataAdmin/CRUD/show_amount.html.twig'
            ])
            ->add('createdAt', null, [
                'label' => 'form.contributedAt.label',
                'template' => 'bundles/SonataAdmin/CRUD/show_datetime.html.twig'
            ])
            ->add('status', null, [
                'label' => 'form.status.label',
                'template' => 'bundles/SonataAdmin/CRUD/show_status.html.twig'
            ])
        ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create')
        ->remove('edit')
        ->remove('delete');
    }

}