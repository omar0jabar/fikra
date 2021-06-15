<?php

namespace App\Admin;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Vich\UploaderBundle\Form\Type\VichImageType;

final class FondAdmin extends AbstractAdmin
{
   protected $baseRouteName = 'sonata_admin_fond';
   protected $baseRoutePattern = 'fond';
   protected $classnameLabel = 'Fond';

  protected $datagridValues = [
      '_sort_order' => 'DESC',
      '_sort_by' => 'id',
  ];

   protected function configureFormFields(FormMapper $formMapper)
   {
      $objectId = $this->getRoot()->getSubject()->getId();
      $required = (is_null($objectId) ? true : false);
      $formMapper
         ->with('Content', ['class' => 'col-md-6'])
            ->add('local', ChoiceType::class, [
                   'choices' => [
                       'fr' => 'fr',
                       'en' => 'en'
                   ]
               ])
            ->add('title', null, [
               'required' => $required,
            ])
            ->add('sortDesctiption',null,[
                  'label' => "Short description"
            ])
            ->add('content', CKEditorType::class)
            ->add('phone', null, [
                  'required' => false
            ])
            ->add('mail', EmailType::class, [
                  'required' => false
            ])
            ->add('url', null, [
                  'required' => false
            ])
            ->add('uploadLogo', VichImageType::class, [
               'required' => false,
            ])
            ->add('uploadImg', VichImageType::class, 
               [
                  'required' => false,
                  'label' => "Upload background image"

               ])
            
         ->end()

         ->with('Select', ['class' => 'col-md-6'])
            ->add('secteurType')
            ->add('gestionnaires')
            ->add('fondType',null,['label' => "Type de programme"])
            ->add('secteurType',null,['label' => "Secteur"])
            ->add('fondPhases',null,['label' => "Phase de développement"])
            ->add('financements',null,['label' => "Financement proposé"])
            ->add('eligibiliteCritere',null, ['label' => "Critère(s) d’éligibilité"])
            ->add('depenses',null, ['label' => "Type de dépense couvertes"])
            ->add('min', EntityType::class, array(
                'class'        => 'App\Entity\Montant',
                'choice_label' => 'montantMin',
                'required' => false,
              ))
            
            ->add('max', EntityType::class, array(
                'class'        => 'App\Entity\Montant',
                'choice_label' => 'montantMax',
                'required' => false,
              ))
            //->add('amount',null, ['label' => "Montant"])
            ->add('active', null, [
                  'required' => false
            ])
         ->end()
      ;
   }
   
   protected function configureDatagridFilters(DatagridMapper $datagridMapper)
   {
      $datagridMapper
         ->add('local')
         ->add('title')
         ->add('phone')
         ->add('mail')
         ->add('gestionnaires')
         ->add('fondType')
         ->add('secteurType')
         ->add('fondPhases')
         ->add('financements')
         ->add('eligibiliteCritere')
         ->add('depenses')
         ->add('min')
         ->add('url')
         ->add('active')
      ;
   }

   protected function configureListFields(ListMapper $listMapper)
   {
      unset($this->listModes['mosaic']);
      $listMapper
         ->add('local',null,['label' => "Langue"])
         ->add('title')
         ->add('phone')
         ->add('mail')
         ->add('gestionnaires')
         ->add('fondType',null,['label' => "Type de programme"])
         ->add('secteurType',null,['label' => "Secteur"])
         ->add('fondPhases',null,['label' => "Phase de développement"])
         ->add('financements',null,['label' => "Financement proposé"])
         //->add('eligibiliteCritere',null, ['label' => "Critère(s) d’éligibilité"])
         ->add('depenses',null, ['label' => "Type de dépense couvertes"])
         //->add('amount',null, ['label' => "Montant"])
         //->add('url')
         ->add('active',null, ['label' => "Actif"])
         ->add('_action', null, [
            'actions' => [
               'preview' => [
                  'template' => 'bundles/SonataAdmin/CRUD/preview.html.twig',
               ],
               'show' => [],
               'edit' => [],
               'delete' => [],
            ]
         ])
      ;
   }

   protected function configureShowFields(ShowMapper $showMapper)
   {
      $showMapper
         ->with('', ['class' => 'col-md-6'])
         ->add('local')
         ->add('title')
         ->add('phone')
         ->add('mail')
         ->add('gestionnaires')
         ->add('fondType')
         ->add('secteurType')
         ->add('fondPhases')
         ->add('financements')
         ->add('eligibiliteCritere')
         ->add('depenses')
         ->add('min')
         ->add('url')
         ->end()
      ;
   }
   protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('preview', $this->getRouterIdParameter().'/preview');
    }

}