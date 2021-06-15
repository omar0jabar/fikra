<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Vich\UploaderBundle\Form\Type\VichImageType;

final class PartnerAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'sonata_admin_partner';
    protected $baseRoutePattern = 'partner';
    protected $classnameLabel = 'Partner';

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
			->add('name')
			->add('url')
			;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
       unset($this->listModes['mosaic']);

        $listMapper
			->addIdentifier('id')
			->addIdentifier('name', null, [
            'label' => 'nom'
         ])
			->add('url')
			->add('createdAt', null, [
            'template' => 'bundles/SonataAdmin/CRUD/list_datetime.html.twig'
         ])
			->add('updatedAt', null, [
            'template' => 'bundles/SonataAdmin/CRUD/list_datetime.html.twig'
         ])
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
			->add('name')
			->add('logoFile', VichImageType::class, [
			    'required' => false,
            ])
			->add('url')
			;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
			->add('id')
			->add('name', null, [
			   'label' => 'nom'
         ])
			->add('logoFile')
			->add('url')
			->add('createdAt', null, [
            'template' => 'bundles/SonataAdmin/CRUD/show_datetime.html.twig'
         ])
			->add('updatedAt', null, [
            'template' => 'bundles/SonataAdmin/CRUD/show_datetime.html.twig'
         ])
			;
    }
}
