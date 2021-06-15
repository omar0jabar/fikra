<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class FundingObjectiveAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'sonata_admin_funding_objective';
    protected $baseRoutePattern = 'funding-objective';
    protected $classnameLabel = 'Funding Objective';

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('min', TextType::class, [
            'required' => false,
        ])
            ->add('max', TextType::class);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('min')
            ->add('max');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        unset($this->listModes['mosaic']);

        $listMapper->addIdentifier('id')
            ->add('min')
            ->add('max')
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
            ->add('min')
            ->add('max')
        ;
    }

}
