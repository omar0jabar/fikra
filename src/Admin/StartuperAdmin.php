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

final class StartuperAdmin extends AbstractAdmin
{
    private $repoRole;

    protected $baseRouteName = 'sonata_admin_startuper';
    protected $baseRoutePattern = 'startuper';
    protected $classnameLabel = 'Startuper';
    protected $datagridValues = [
        '_sort_order' => 'DESC',
        '_sort_by' => 'id',
    ];

    public function __construct(string $code, string $class, string $baseControllerName, RoleRepository $repoRole)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->repoRole = $repoRole;
    }

    public function createQuery($context = 'list')
    {
        $roleStartuper = $this->repoRole->findOneBy(['label' => "ROLE_STARTUPER"]);
        $query = parent::createQuery($context);
        $alias = $query->getRootAliases()[0];

        if ($roleStartuper !== null) {
            $query->andWhere(
                $query->expr()->eq($alias . '.role', ':role')
            )
                ->andWhere($query->expr()->eq($alias . '.isDeleted', ':value'))
                ->setParameter('value', false)
                ->setParameter('role', $roleStartuper->getId());
        }
        return $query;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Profile', ['class' => 'col-md-6'])
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('phone')
            ->add('birthday', DateType::class)
            ->end()
            ->with('Account', ['class' => 'col-md-6'])
            ->add('email', TextType::class)
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('firstName')
            ->add('lastName')
            ->add('phone')
            ->add('email')
            ->add('isActive')
            ->add('isBanned');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        unset($this->listModes['mosaic']);

        $listMapper
            ->addIdentifier('firstName')
            ->add('lastName')
            ->add('email')
            ->add('phone')
            ->add('isActive')
            ->add('isBanned', null, [
                'template' => 'bundles/SonataAdmin/CRUD/list_banned.html.twig'
            ])
            ->add('lastLogin', null, [
                'template' => 'bundles/SonataAdmin/CRUD/list_datetime.html.twig'
            ])
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                    'lock' => [
                        'template' => 'bundles/SonataAdmin/CRUD/list__action_lock.html.twig',
                    ],
                ]
            ]);;
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('Profile', ['class' => 'col-md-6'])
            ->add('firstName')
            ->add('lastName')
            ->add('phone')
            ->add('city')
            ->add('socialReason')
            ->end()
            ->with('Account', ['class' => 'col-md-6'])
            ->add('profile')
            ->add('email')
            ->add('isActive')
            ->add('isBanned', null, [
                'template' => 'bundles/SonataAdmin/CRUD/show_banned.html.twig'
            ])
            ->add('lastLogin', null, [
                'template' => 'bundles/SonataAdmin/CRUD/show_datetime.html.twig'
            ])
            ->add('createdAt', null, [
                'template' => 'bundles/SonataAdmin/CRUD/show_datetime.html.twig'
            ])
            ->add('updatedAt', null, [
                'template' => 'bundles/SonataAdmin/CRUD/show_datetime.html.twig'
            ])
            ->end();
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add('lock', $this->getRouterIdParameter() . '/lock');
        $collection->remove('create');
    }

}