<?php

namespace App\Admin;

use App\Entity\Role;
use App\Form\ReassuranceColType;
use App\Repository\RoleRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Vich\UploaderBundle\Form\Type\VichImageType;

final class ReassuranceAdmin extends AbstractAdmin
{
   protected $baseRouteName = 'sonata_admin_reassurance_block';
   protected $baseRoutePattern = 'reassurance-block';
   protected $classnameLabel = 'Reassurance block';

   protected function configureFormFields(FormMapper $formMapper)
   {
      $formMapper
         ->with('', ['class' => 'col-md-6'])
         ->add('title')
         ->add('paragraph')
         ->add('lang', ChoiceType::class, [
             'choices' => [
                 'Francais' => 'fr',
                 'Anglais' => 'en'
             ]
         ])
          ->add('cols', CollectionType::class, [
              "entry_type" => ReassuranceColType::class,
              "by_reference" => false,
              "allow_delete" => true,
              "allow_add" => true,
              //'label' => 'form.avantages.label',
          ])
         ->end()
      ;
   }

   protected function configureDatagridFilters(DatagridMapper $datagridMapper)
   {
      $datagridMapper
          ->add('title')
          ->add('lang')
          ->add('createdAt')
          ->add('updatedAt')
      ;
   }

   protected function configureListFields(ListMapper $listMapper)
   {
      unset($this->listModes['mosaic']);

      $listMapper
         ->addIdentifier('title')
         ->add('lang')
         ->add('createdAt')
         ->add('updatedAt')
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
         ->with('Reassurance Block', ['class' => 'col-md-6'])
         ->add('title')
         ->add('paragraph')
         ->add('lang')
         ->add('createdAt')
         ->add('updatedAt')
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