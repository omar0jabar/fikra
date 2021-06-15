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

final class BackgroundSliderAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'sonata_admin_background_slider';
    protected $baseRoutePattern = 'background-slider';
    protected $classnameLabel = 'Background slider';

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('title')
            ->add('language')
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        unset($this->listModes['mosaic']);

        $listMapper
            ->addIdentifier('id')
            ->add('title')
            ->addIdentifier('language')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => []
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $data = $this->subject;
        $required = true;
        if (!empty($data->getId())) { $required = false; }
        $formMapper
            ->add('title')
            ->add('bannerMobileFile', VichImageType::class, [
                'required' => $required
            ])
            ->add('bannerDesktopFile', VichImageType::class, [
                'required' => $required
            ])
            ->add('language', ChoiceType::class, [
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
            ->add('title')
            ->add('bannerMobileFile')
            ->add('bannerDesktopFile')
            ->add('language')
            ->add('createdAt', null, [
                'template' => 'bundles/SonataAdmin/CRUD/show_datetime.html.twig'
            ])
            ->add('updatedAt', null, [
                'template' => 'bundles/SonataAdmin/CRUD/show_datetime.html.twig'
            ])
        ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('create')
            ->remove('delete')
            ;
    }
}
