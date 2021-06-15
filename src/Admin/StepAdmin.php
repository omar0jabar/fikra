<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class StepAdmin extends AbstractAdmin
{
   protected $baseRouteName = 'sonata_admin_step';
   protected $baseRoutePattern = 'step';
   protected $classnameLabel = 'Step';

   protected function configureFormFields(FormMapper $formMapper)
   {
      $formMapper
          ->add('labelFr', TextType::class)
          ->add('helpFr', TextType::class)
          ->add('labelEn', TextType::class)
          ->add('helpEn', TextType::class)
      ;
   }

   protected function configureDatagridFilters(DatagridMapper $datagridMapper)
   {
      $datagridMapper->add('labelFr');
      $datagridMapper->add('labelEn');
   }

   protected function configureListFields(ListMapper $listMapper)
   {
      unset($this->listModes['mosaic']);

      $listMapper
          ->addIdentifier('id')
          ->addIdentifier('labelFr')
          ->addIdentifier('labelEn')
            ->add('_action', null, [
                  'actions' => [
                        'show' => [],
                        'edit' => [],
                        'delete' => [],
                  ]
            ]);
      ;
   }

   protected function configureShowFields(ShowMapper $showMapper): void
   {
      $showMapper
            ->add('id')
            ->add('labelFr')
            ->add('helpFr')
            ->add('labelEn')
            ->add('helpEn')
            ->add('createdAt')
            ->add('updatedAt')
      ;
   }

}
