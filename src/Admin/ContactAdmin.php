<?php

namespace App\Admin;

use App\Repository\RoleRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

final class ContactAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'sonata_admin_contact';
    protected $baseRoutePattern = 'contact';
    protected $classnameLabel = 'Contact';
    protected $datagridValues = [
        '_sort_order' => 'DESC',
        '_sort_by' => 'id',
    ];

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('email')
            ->add('object')
            ->add('createdAt')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        unset($this->listModes['mosaic']);
        $listMapper
            ->addIdentifier('email')
            ->add('phone')
            ->add('object')
            ->add('createdAt', null, [
                'template' => 'bundles/SonataAdmin/CRUD/list_datetime.html.twig'
            ])
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'delete' => [],
                ]
            ]);
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('firstName')
            ->add('lastName')
            ->add('email')
            ->add('phone')
            ->add('object')
            ->add('message')
            ->add('createdAt', null, [
                'template' => 'bundles/SonataAdmin/CRUD/show_datetime.html.twig'
            ])
        ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create')
                    ->remove('edit')
                    ->remove('export');
    }

}