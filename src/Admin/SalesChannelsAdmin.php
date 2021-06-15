<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class SalesChannelsAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'sonata_admin_sales_channels';
    protected $baseRoutePattern = 'sales-channels';
    protected $classnameLabel = 'Sales channels';

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('labelFr', TextType::class)
            ->add('labelEn', TextType::class)
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('labelFr')
            ->add('labelEn')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        unset($this->listModes['mosaic']);

        $listMapper
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
            ->add('labelEn')
            ->add('createdAt')
            ->add('updatedAt')
        ;
    }

}
