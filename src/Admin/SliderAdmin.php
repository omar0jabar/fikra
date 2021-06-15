<?php

namespace App\Admin;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Vich\UploaderBundle\Form\Type\VichImageType;

final class SliderAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'sonata_admin_slider';
    protected $baseRoutePattern = 'slider';
    protected $classnameLabel = 'Slider';
    protected $datagridValues = [
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt',
    ];

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('title')
            ->add('lang')
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        unset($this->listModes['mosaic']);

        $listMapper
            ->addIdentifier('title')
            ->addIdentifier('lang')
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
            ->add('imageFile', VichImageType::class, [
                'required' => $required,
                'help' => 'Format allowed: JPG, JPEG, PNG. (size:1400x900px)'
            ])
            ->add('imageMobileFile', VichImageType::class, [
                'required' => false,
                'help' => 'Format allowed: JPG, JPEG, PNG. (size:840x500px)'
            ])
            ->add('colorText', ChoiceType::class, [
                'choices' => [
                    'Texte noir' => 'text-black',
                    'Texte Blanc' => 'text-white',
                ]
            ])
            ->add('title')
            ->add('content', CKEditorType::class)
            ->add('button', CKEditorType::class, [
                'required' => false
            ])
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
            ->add('imageFile')
            ->add('imageMobileFile')
            ->add('colorText')
            ->add('title')
            ->add('content')
            ->add('lang')
            ->add('createdAt', null, [
                'template' => 'bundles/SonataAdmin/CRUD/show_datetime.html.twig'
            ])
            ->add('updatedAt', null, [
                'template' => 'bundles/SonataAdmin/CRUD/show_datetime.html.twig'
            ])
        ;
    }
}
