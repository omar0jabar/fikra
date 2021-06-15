<?php

namespace App\Admin;

use App\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

final class RequestDocumentationAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'sonata_admin_request_documentation';
    protected $baseRoutePattern = 'request-documentation';
    protected $classnameLabel = 'request documentation';
    protected $datagridValues = [
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt',
    ];

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('project')
            ->add('createdAt')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        unset($this->listModes['mosaic']);

        $listMapper
            ->addIdentifier('user', EntityType::class, [
                'class' => User::class,
                'property' => 'email',
                'admin_code' => 'sonata.admin.startuper',
                'label' => 'form.author.label'
            ])
            ->add('project')
            ->add('isAccepted', null, [
                'template' => 'bundles/SonataAdmin/CRUD/list_accepted.html.twig',
            ])
            ->add('createdAt')
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ]
            ]);
        ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('create')
            ->remove('edit')
            ->remove('delete')
            ->add('accept', $this->getRouterIdParameter().'/accept')
            ->add('refuse', $this->getRouterIdParameter().'/refuse')
        ;
    }

}
