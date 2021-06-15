<?php

declare(strict_types=1);

namespace EgioDigital\CMSBundle\Admin;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Sonata\AdminBundle\Show\ShowMapper;
use Vich\UploaderBundle\Form\Type\VichImageType;

use EgioDigital\CMSBundle\Repository\CategoryPageRepository;

final class PageAdmin extends AbstractAdmin
{
   protected $baseRouteName = 'sonata_admin_page';
   protected $baseRoutePattern = 'page';
   protected $classnameLabel = 'Page';
   protected $repo ;
   protected $datagridValues = [
      '_sort_order' => 'DESC',
      //'_sort_by' => 'updatedAt',
   ];


   protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
   {
      $datagridMapper
         ->add('title')
         ->add('lang')
         ->add('category');
   }

   protected function configureListFields(ListMapper $listMapper): void
   {
      unset($this->listModes['mosaic']);

      $listMapper
         ->addIdentifier('id')
         ->addIdentifier('title')
         ->add('lang')
         ->add('category')
         ->add('isActive')
         ->add('_action', null, [
            'actions' => [
               'show' => [],
               'edit' => [],
               'delete' => [],
               'clone' => [
                  'template' => '@CMSBundleViews/views/CRUD/clone.html.twig',
               ],
                'publish' => [
                    'template' => '@CMSBundleViews/views/CRUD/publish.html.twig',
                ],
            ],
         ]);
   }

   protected function configureFormFields(FormMapper $formMapper): void
   {
      $objectId = $this->getRoot()->getSubject()->getId();
      $required = (is_null($objectId) ? true : false);

      if (!is_null($objectId)) {
         $formMapper->tab('Page');
      }
      $formMapper->with('Page', ['class' => 'col-md-9'])
         ->add('category')
         ->add('title')
         ->add('slug', TextType::class, ['required' => false])
         ->add('metaTitle')
         ->add('metaTags')
         ->add('metaDescription')
         ->add('uploadBannerDesktop', VichImageType::class, ['required' => $required])
         ->add('uploadBannerMobile', VichImageType::class, ['required' => $required])
         ->add('isActive')
         ->end();
      if (!is_null($objectId)) {
         $formMapper->with('Blocks', ['class' => 'col-md-3'])
            ->end();
      }
      if (!is_null($objectId)) {
         $formMapper->end();
      }

      ;
      if (!is_null($objectId)) {
         $formMapper->tab('Add Block')

            ->end();
      }




   }

   protected function configureShowFields(ShowMapper $showMapper): void
   {
      $showMapper
         ->add('id')
         ->add('title')
         ->add('metaTags')
         ->add('metaDescription')
         ->add('category')

         ->add('isActive')
         ->add('metaContent', null, [
               'template' => 'SonataAdminBundle/views/CRUD/row.html.twig'
            ]
         )
         ->add('createdAt')
         ->add('updatedAt')
      ;
   }

   protected function configureRoutes(RouteCollection $collection)
   {
      $collection
         ->add('clone', $this->getRouterIdParameter().'/clone')
         ->add('publish', $this->getRouterIdParameter().'/publish');
   }
}
