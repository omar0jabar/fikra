<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Vich\UploaderBundle\Form\Type\VichImageType;

final class financementTypeAdmin extends AbstractAdmin
{
   protected $baseRouteName = 'sonata_admin_financementType';
   protected $baseRoutePattern = 'financementType';
   protected $classnameLabel = 'FinancementType';

   protected function configureFormFields(FormMapper $formMapper)
   {
      $formMapper
         ->with('', ['class' => 'col-md-6'])
         ->add('name')
         ->add('nameEn')
         ->end()
      ;
   }

   protected function configureDatagridFilters(DatagridMapper $datagridMapper)
   {
      $datagridMapper->add('name')
         ->add('nameEn')
      ;
   }

   protected function configureListFields(ListMapper $listMapper)
   {

      $listMapper
         ->add('name')
         ->add('nameEn')
         ->add('_action', null, [
            'actions' => [
               'show' => [],
               'edit' => [],
               'delete' => []
            ]
         ])
      ;
   }

   protected function configureShowFields(ShowMapper $showMapper)
   {
      $showMapper
         ->with('', ['class' => 'col-md-6'])
         ->add('name')
         ->add('nameEn')
         ->end()
      ;
   }

}