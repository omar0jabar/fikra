<?php

namespace App\Admin;

use App\Entity\Role;
use App\Repository\RoleRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class AdminAdmin extends AbstractAdmin
{
    private $repoRole;

    protected $baseRouteName = 'sonata_admin_admin';
    protected $baseRoutePattern = 'admin';
    protected $classnameLabel = 'Admin';
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
        if ($roleStartuper !== null) {
            $query->andWhere(
                $query->expr()->neq($query->getRootAliases()[0] . '.role', ':role')
            )
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
            ->add('birthday', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('address', TextareaType::class, [
                'required' => false
            ])
            ->end()
            ->with('Account', ['class' => 'col-md-6'])
            ->add('email', TextType::class)
            ->add('role', null, [
                'query_builder' => function (RoleRepository $role) {
                    return $role->createQueryBuilder('r')
                        ->where('r.label != :role')
                        ->setParameter('role', "ROLE_STARTUPER");
                },
            ])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('email')
            ->add('phone')
            ->add('role')
            ->add('isBanned');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        unset($this->listModes['mosaic']);

        $listMapper
            ->add('avatar', null, [
                    'template' => 'bundles/SonataAdmin/CRUD/list_avatar-mini.html.twig'
                ]
            )
            ->addIdentifier('firstName')
            ->add('lastName')
            ->add('phone')
            ->add('email')
            ->add('role', ModelType::class, [
                'class' => Role::class,
                "choice_label" => 'label'
            ])
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
            ]);
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $templateDateTime = 'bundles/SonataAdmin/CRUD/show_datetime.html.twig';
        $showMapper
            ->with('Profile', ['class' => 'col-md-6'])
            ->add('avatar', null, [
                'template' => 'bundles/SonataAdmin/CRUD/list_avatar.html.twig'
            ])
            ->add('firstName')
            ->add('lastName')
            ->add('phone')
            ->add('Address')
            ->end()
            ->with('Account', ['class' => 'col-md-6'])
            ->add('email')
            ->add('isActive')
            ->add('isBanned', null, [
                'template' => 'bundles/SonataAdmin/CRUD/show_banned.html.twig'
            ])
            ->add('lastLogin', null, [
                'template' => $templateDateTime
            ])
            ->add('createdAt', null, [
                'template' => $templateDateTime
            ])
            ->add('updatedAt', null, [
                'template' => $templateDateTime
            ])
            ->end();
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add('lock', $this->getRouterIdParameter() . '/lock');
    }

}