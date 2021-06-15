<?php

namespace App\Admin;

use App\Entity\Role;
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
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Vich\UploaderBundle\Form\Type\VichImageType;

final class CommentCaMarcheBlockAdmin extends AbstractAdmin
{
   protected $baseRouteName = 'sonata_admin_comment_ca_marche';
   protected $baseRoutePattern = 'comment-ca-marche';
   protected $classnameLabel = 'Comment ça marche';

   protected function configureFormFields(FormMapper $formMapper)
   {
      $formMapper
         ->with('', ['class' => 'col-md-6'])
         ->add('imageFile', VichImageType::class, [
            'required' => false
         ])
         ->add('backgroundColor')

         ->add('title')
         ->add('description', CKEditorType::class)
         ->add('btn1Text')
         ->add('btn1Link')
         ->add('btn1Target', ChoiceType::class, [
            'choices' => [
               'Ouvrir dans une nouvelle onglet' => "_blank",
               'Ouvrir dans l\'onglet actuel' => "_self"
            ]
         ])
         ->add('btn2Text')
         ->add('btn2Link')
         ->add('btn2Target', ChoiceType::class, [
            'choices' => [
               'Ouvrir dans une nouvelle onglet' => "_blank",
               'Ouvrir dans l\'onglet actuel' => "_self"
            ]
         ])
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
         ->add('lang')
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
         ->with('Profile', ['class' => 'col-md-6'])
         ->add('image', null, [
               'template' => 'bundles/SonataAdmin/CRUD/show_image.html.twig'
            ]
         )
         ->add('title')
         ->add('description')
         ->add('btn1Text')
         ->add('btn1Link')
         ->add('btn2Text')
         ->add('btn2Link')
         ->add('type')
         ->add('backgroundColor')
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