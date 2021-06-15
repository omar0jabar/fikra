<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

final class VideoAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'sonata_admin_video';
    protected $baseRoutePattern = 'video';
    protected $classnameLabel = 'Video';

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('', ['class' => 'col-md-6'])
            ->add('title')
            ->add('url')
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
            ->add('url')
            ->add('language')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        unset($this->listModes['mosaic']);

        $listMapper
            ->addIdentifier('title')
            ->addIdentifier('url')
            ->add('language')
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                ]
            ])
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('Profile', ['class' => 'col-md-6'])
            ->add('title')
            ->add('url')
            ->add('language')
            ->end()
        ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('delete')
            ->remove('create')
        ;
    }

}