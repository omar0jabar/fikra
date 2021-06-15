<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class ContactInfoAdmin extends AbstractAdmin
{
   protected $baseRouteName = 'sonata_admin_contact_info';
   protected $baseRoutePattern = 'contact-info';
   protected $classnameLabel = 'Contact info';

   protected function configureFormFields(FormMapper $formMapper)
   {
      $formMapper
          ->add('info', TextType::class)
      ;
   }

   protected function configureDatagridFilters(DatagridMapper $datagridMapper)
   {
      $datagridMapper->add('title');
   }

   protected function configureListFields(ListMapper $listMapper)
   {
      unset($this->listModes['mosaic']);

      $listMapper
          ->addIdentifier('id')
          ->addIdentifier('title')
          ->add('info')
            ->add('_action', null, [
                  'actions' => [
                        'show' => [],
                        'edit' => [],
                  ]
            ]);
      ;
   }

   protected function configureShowFields(ShowMapper $showMapper): void
   {
      $showMapper
            ->add('id')
            ->add('title')
            ->add('info')
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
