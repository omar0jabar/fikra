<?php

namespace EgioDigital\CMSBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class OfferRequestAdmin extends AbstractAdmin
{
   protected $baseRouteName = 'sonata_admin_offer_request';
   protected $baseRoutePattern = 'offer-request';
   protected $classnameLabel = 'Offer request';

   protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
   {
      $datagridMapper
         ->add('firstName')
         ->add('email')
         ->add('block');
   }

   protected function configureListFields(ListMapper $listMapper): void
   {
      unset($this->listModes['mosaic']);

      $listMapper
         ->addIdentifier('id')
          ->addIdentifier('email')
          ->add('firstName')
          ->add('block')
          ->add('createdAt')
         ->add('_action', null, [
            'actions' => [
               'show' => [],
               'edit' => [],
               'delete' => [],
            ],
         ]);
   }


   protected function configureShowFields(ShowMapper $showMapper): void
   {
      $showMapper
         ->add('id')
         ->add('firstName')
         ->add('lastName')
         ->add('email')
         ->add('type')
         ->add('block')
         ->add('message')
         ->add('createdAt')
      ;
   }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('create')
            ->remove('edit');
    }
}
