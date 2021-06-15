<?php

declare(strict_types=1);

namespace EgioDigital\CMSBundle\Admin;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

final class EventAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'sonata_admin_event';
    protected $baseRoutePattern = 'event';
    protected $classnameLabel = 'Event';
    protected $datagridValues = [
        '_sort_order' => 'DESC',
        //'_sort_by' => 'updatedAt',
    ];

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('title')
            ->add('category')
            ->add('lang');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        unset($this->listModes['mosaic']);

        $listMapper
            ->addIdentifier('id', null, [
                'route' => [
                    'name' => 'show'
                ]
            ])
            ->addIdentifier('title')
            ->add('dateDebut')
            //->add('heureDebut')
            ->add('dateFin')
            //->add('heureFin')
            ->add('lang')
            ->add('category')
            ->add('isExpired', null, [
                'template' => 'bundles/SonataAdmin/CRUD/list_expired.html.twig',
                'label' => 'Expired'
            ])
            ->add('isActive')
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                    'clone' => [
                        'template' => '@CMSBundleViews/views/CRUD/clone.html.twig',
                    ],
                    'publish' => [
                        'template' => '@CMSBundleViews/views/CRUD/publish.html.twig',
                    ],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('category')
            ->add('title')
            ->add('url', TextType::class, ['required' => false])
            ->add('metaTitle')
            ->add('metaTags')
            ->add('metaDescription')
            ->add('content', CKEditorType::class)
            ->add('isActive');
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('title')
            ->add('metaTags')
            ->add('metaDescription')
            ->add('category')
            ->add('dateDebut')
            ->add('dateFin')
            ->add('lieu')
            ->add('url')
            ->add('isActive')
            ->add('content', null, [
                    'template' => 'SonataAdminBundle/views/CRUD/row.html.twig'
                ]
            )
            ->add('lang')
            ->add('createdAt')
            ->add('updatedAt');
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add('clone', $this->getRouterIdParameter() . '/clone')
            ->add('publish', $this->getRouterIdParameter() . '/publish');
    }
}
