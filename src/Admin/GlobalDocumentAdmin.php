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
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

final class GlobalDocumentAdmin extends AbstractAdmin
{

   protected $baseRouteName = 'sonata_admin_global_document';
   protected $baseRoutePattern = 'global-document';
   protected $classnameLabel = 'Global Document';

    protected $datagridValues = [
        '_sort_order' => 'DESC',
        '_sort_by' => 'updatedAt',
    ];


   protected function configureFormFields(FormMapper $formMapper)
   {
      $formMapper
         ->with('Document', ['class' => 'col-md-12'])
            ->add('type', ChoiceType::class, [
                'choices' => [
                  'Document publics' => 'public',
                  'Auto production' => 'auto_production'
               ],
            ])
            ->add('title', TextType::class, ['required' => false])
            ->add('date', null, [
               'widget' => 'single_text'
            ])
            ->add('uploadFile', VichFileType::class, ['required' => false])
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
         ->add('type')
         ->add('date')
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
         ->add('type')
         ->add('title')
         ->add('countDownload')
         ->add('createdAt')
         ->add('updatedAt')
         ->end()
      ;
   }

}