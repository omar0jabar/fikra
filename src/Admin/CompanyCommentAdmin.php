<?php

namespace App\Admin;

use App\Form\CompanyCommentResponseType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class CompanyCommentAdmin
 * @package App\Admin
 */
final class CompanyCommentAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'sonata_admin_company_comment';
    protected $baseRoutePattern = 'comment';
    protected $classnameLabel = 'Comment';
    protected $translationDomain = 'company';
    protected $datagridValues = [
        '_sort_order' => 'DESC',
        '_sort_by' => 'id',
    ];

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('content', TextType::class)
            ->add('isPublished', null, [
                'label' => 'form.isPublished.label'
            ])
            ->add('responses', CollectionType::class, [
                'entry_type' => CompanyCommentResponseType::class,
                'allow_add' => true,
                'allow_delete' => true
            ]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            //->add('author')
            ->add('company')
            ->add('createdAt')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        unset($this->listModes['mosaic']);

        $listMapper
            ->addIdentifier('id')
            /*->add('author', null, [
                'class' => User::class,
                'admin_code' => 'sonata.admin.startuper'
            ])*/
            ->add('company')
            ->add('content')
            ->add('isPublished', null, [
                'label' => 'form.isPublished.label'
            ])
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                    /*'publish' => [
                        //'template' => 'bundles/SonataAdmin/CRUD/list__action_lock.html.twig',
                    ],*/
                ]
            ]);
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            //->add('author')
            ->add('company')
            ->add('content')
            ->add('isPublished', null, [
                'label' => 'form.isPublished.label'
            ])
            ->add('responses')
        ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        //$collection->add('lock', $this->getRouterIdParameter() . '/lock');
        //$collection->remove('create');
    }

}