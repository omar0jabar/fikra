<?php

declare(strict_types=1);

namespace EgioDigital\CMSBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class CategoryPageAdmin extends AbstractAdmin
{
   protected $baseRouteName = 'sonata_admin_category_page';
   protected $baseRoutePattern = 'category-page';
   protected $classnameLabel = 'Category Page';

   protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
   {
      $datagridMapper
         ->add('title')
         ->add('lang');
   }

   protected function configureListFields(ListMapper $listMapper): void
   {
      unset($this->listModes['mosaic']);

      $listMapper
         ->addIdentifier('id')
         ->addIdentifier('title')
         ->add('lang')
         ->add('_action', null, [
            'actions' => [
               'show' => [],
               'edit' => [],
               'delete' => [],
            ],
         ]);
   }

   protected function configureFormFields(FormMapper $formMapper): void
   {
      $formMapper
         ->add('title')
         ->add('lang', ChoiceType::class, [
            'label' => 'Langue*',
            'choices' => [
               'Anglais' => 'en',
               'Français' => 'fr'
            ]
         ]);
   }

   protected function configureShowFields(ShowMapper $showMapper): void
   {
      $showMapper
         ->add('id')
         ->add('title')
         ->add('lang')
         ->add('createdAt')
         ->add('updatedAt')
      ;
   }
}
