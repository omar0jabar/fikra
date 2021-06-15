<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Vich\UploaderBundle\Form\Type\VichImageType;

final class ToolsAdmin extends AbstractAdmin
{


   protected $baseRouteName = 'sonata_admin_tools';
   protected $baseRoutePattern = 'tools';
   protected $classnameLabel = 'Tools';
   protected $datagridValues = [
        '_sort_order' => 'DESC',
        '_sort_by' => 'updatedAt',
   ];

   protected function configureFormFields(FormMapper $formMapper)
   {
      $formMapper
         ->with('Tools', ['class' => 'col-md-12'])
            ->add('icon', ChoiceType::class, [
                'choices' => [
                    [
                        'icon 0' => '0',
                        'icon 1' => '1',
                        'icon 2' => '2',
                    ]
                ],
                'expanded'=>true,
            ])
            ->add('title', TextType::class)
            ->add('content', TextareaType::class)
            ->add('uploadFile', VichImageType::class)
         ->end()
      ;
   }

   protected function configureDatagridFilters(DatagridMapper $datagridMapper)
   {
      $datagridMapper->add('title')
      ;
   }

   protected function configureListFields(ListMapper $listMapper)
   {
      unset($this->listModes['mosaic']);

      $listMapper
         ->addIdentifier('title')
         ->add('countDownload')
         ->add('createdAt')
         ->add('updatedAt')
         ->add('_action', null, [
            'actions' => [
               'show' => [],
               'edit' => [],
               'delete' => []
            ]
         ]);
      ;
   }

   protected function configureShowFields(ShowMapper $showMapper)
   {
      $showMapper
         ->with('Profile', ['class' => 'col-md-12'])
         ->add('title')
         ->add('content')
         ->add('countDownload')
         ->add('createdAt')
         ->add('updatedAt')
         ->end()
      ;
   }

}