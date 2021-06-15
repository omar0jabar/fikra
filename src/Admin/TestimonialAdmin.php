<?php

namespace App\Admin;

use App\Repository\RoleRepository;
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

final class TestimonialAdmin extends AbstractAdmin
{
   protected $baseRouteName = 'sonata_admin_testimonial';
   protected $baseRoutePattern = 'testimonial';
   protected $classnameLabel = 'Testimonial';

   protected function configureFormFields(FormMapper $formMapper)
   {
      $formMapper
         ->with('', ['class' => 'col-md-6'])
         ->add('fullName')
         ->add('position')
         ->add('description')
         ->add('imageFile', VichImageType::class)
         ->end()
      ;
   }

   protected function configureDatagridFilters(DatagridMapper $datagridMapper)
   {
      $datagridMapper->add('fullName')
         ->add('position')
      ;
   }

   protected function configureListFields(ListMapper $listMapper)
   {
      unset($this->listModes['mosaic']);

      $listMapper
         ->addIdentifier('avatar', null, [
            'template' => 'bundles/SonataAdmin/CRUD/list_avatar-mini.html.twig'
         ])
         ->addIdentifier('fullName')
         ->add('position')
         ->add('createdAt')
         ->add('_action', null, [
            'actions' => [
               'show' => [],
               'edit' => [],
               'delete' => [],
            ]
         ]);
      ;
   }

   protected function configureShowFields(ShowMapper $showMapper)
   {
      $showMapper
         ->with('', ['class' => 'col-md-6'])
         ->add('id')
         ->add('avatar')
         ->add('fullName')
         ->add('position')
         ->add('description')
         ->add('createdAt')
         ->add('updatedAt')
         ->end()
      ;
   }

}