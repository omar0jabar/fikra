<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

final class DomainAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'sonata_admin_domain';
    protected $baseRoutePattern = 'domain';
    protected $classnameLabel = 'Domain';
    protected $translationDomain = 'company';
    protected $datagridValues = [
        '_sort_order' => 'DESC',
        '_sort_by' => 'id',
    ];

   protected function configureFormFields(FormMapper $formMapper)
   {
      $formMapper
         ->with('', ['class' => 'col-md-6'])
         ->add('labelFr')
         ->add('labelEn')
         ->end()
      ;
   }

   protected function configureDatagridFilters(DatagridMapper $datagridMapper)
   {
      $datagridMapper->add('labelFr')
         ->add('labelEn')
      ;
   }

   protected function configureListFields(ListMapper $listMapper)
   {

      $listMapper
         ->add('labelFr')
         ->add('labelEn')
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
         ->add('labelFr')
         ->add('labelEn')
         ->end()
      ;
   }

}