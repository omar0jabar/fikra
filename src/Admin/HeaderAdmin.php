<?php

namespace App\Admin;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Vich\UploaderBundle\Form\Type\VichImageType;

final class HeaderAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'sonata_admin_header';
    protected $baseRoutePattern = 'header';
    protected $classnameLabel = 'Header';

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('page')
            ->add('lang')
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        unset($this->listModes['mosaic']);

        $listMapper
            ->addIdentifier('page')
            ->add('lang')
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => []
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('bannerFile', VichImageType::class, [
                'required' => false
            ])
            ->add('description', CKEditorType::class)
            ->add('lang', ChoiceType::class, [
                'placeholder' => 'form.lang.placeholder',
                'choices' => [
                    'fr' => 'fr',
                    'en' => 'en'
                ]
            ])
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('page')
            ->add('bannerFile')
            ->add('description')
            ->add('lang')
            ->add('createdAt', null, [
                'template' => 'bundles/SonataAdmin/CRUD/show_datetime.html.twig'
            ])
            ->add('updatedAt', null, [
                'template' => 'bundles/SonataAdmin/CRUD/show_datetime.html.twig'
            ])
        ;
    }

    protected function configureRoutes(RouteCollection $collection): void
    {
        $collection->remove('create');
        $collection->remove('delete');
    }
}
