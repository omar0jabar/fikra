<?php

namespace App\Admin;

use App\Entity\City;
use App\Entity\Domain;
use App\Entity\User;
use App\Form\CompanyDocumentType;
use App\Form\UseFundType;
use App\Helper\DataHelper;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * Class CompanyAdmin
 * @package App\Admin
 */
final class CompanyAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'sonata_admin_company';
    protected $baseRoutePattern = 'company';
    protected $classnameLabel = 'Company';
    protected $translationDomain = 'company';
    protected $datagridValues = [
        '_sort_order' => 'DESC',
        '_sort_by' => 'id',
    ];
    /**
     * @var DataHelper
     */
    private $dataHelper;

    /**
     * CompanyAdmin constructor.
     *
     * @param $code
     * @param $class
     * @param $baseControllerName
     * @param DataHelper $dataHelper
     */
    public function __construct(
        $code,
        $class,
        $baseControllerName,
        DataHelper $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
        parent::__construct($code, $class, $baseControllerName);
    }

    /**
     * @param string $context
     * @return \Sonata\AdminBundle\Datagrid\ProxyQueryInterface
     */
    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);

        $query
            ->andWhere($query->expr()->eq($query->getRootAliases()[0] . '.isDeleted', ':value'))
            ->setParameter('value', false)
        ;
        return $query;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('name', null, [
                'label' => 'form.name.label'
            ])
            ->add('associationName', null, [
                'label' => 'form.associationName.label'
            ])
            ->add('user', null, [
                'class' => User::class,
                'property' => 'firstName',
                'admin_code' => 'sonata.admin.startuper',
            ])
            ->add('city', null, [
                'class' => City::class,
                'property' => 'name',
                'admin_code' => 'sonata.admin.city',
            ])
            ->add('domain', null, [
                'label' => 'form.domain.label'
            ])
            ->add('isDraft', null, [
                'label' => 'form.isDraft.label'
            ])
            ->add('isVerified', null, [
                'label' => 'form.isVerified.label'
            ])
            ->add('isApproved', null, [
                'label' => 'form.isApproved.label'
            ])
            ->add('isUpdated', null, [
                'label' => 'form.isUpdated.label'
            ])
            ->add('isRejected', null, [
                'label' => 'form.isRejected.label'
            ])
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        unset($this->listModes['mosaic']);

        $listMapper
            ->addIdentifier('name', null, [
                'label' => 'form.name.label'
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'property' => 'email',
                'admin_code' => 'sonata.admin.startuper',
                'label' => 'form.user.label'
            ])
            ->add('fundingObjective', null, [
                'template' => 'bundles/SonataAdmin/CRUD/list_fundingObjective.html.twig',
                'label' => 'form.fundingObjective.label'
            ])
            ->add('totalAmount', null, [
                'template' => 'bundles/SonataAdmin/CRUD/list_totalAmountCollected.html.twig',
                'label' => 'form.totalAmount.label'
            ])
            ->add('isDraft', null, [
                'template' => 'bundles/SonataAdmin/CRUD/list_draft.html.twig',
                'label' => 'form.isDraft.label'
            ])
            ->add('isApproved', null, [
                'label' => 'form.isApproved.label'
            ])
            ->add('isRejected', null, [
                'template' => 'bundles/SonataAdmin/CRUD/list_rejected.html.twig',
                'label' => 'form.isRejected.label'
            ])
            ->add('closed', null, [
                'template' => 'bundles/SonataAdmin/CRUD/list_company_closed.html.twig',
                'label' => 'form.isClosed.label'
            ])
            ->add('startDate', null, [
                'label' => 'form.startDate.label',
                'template' => 'bundles/SonataAdmin/CRUD/list_datetime.html.twig',
            ])
            ->add('endDate', null, [
                'label' => 'form.endDate.label',
                'template' => 'bundles/SonataAdmin/CRUD/list_datetime.html.twig',
            ])
            ->add('_action', null, [
                'actions' => [
                    'preview' => [
                        'template' => 'bundles/SonataAdmin/CRUD/list__action_preview.html.twig',
                    ],
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                    'lock' => [
                        'template' => 'bundles/SonataAdmin/CRUD/list__action_lock-project.html.twig',
                    ],
                    'compare' => [
                        'template' => 'bundles/SonataAdmin/CRUD/list__action_compare.html.twig',
                    ],
                    'comments' => [
                        'template' => 'bundles/SonataAdmin/CRUD/list__action_comments.html.twig',
                    ],
                    /*'contributors' => [
                        'template' => 'bundles/SonataAdmin/CRUD/list__action_contributors.html.twig',
                    ],*/
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $locale = \Locale::getDefault();
        $label = 'labelFr';
        if ($locale == 'en') {
            $label = 'labelEn';
        }
        $formMapper
            ->tab('The company')
            ->with('The company', ['translation_domain' => 'company'])
            ->add('name', null, [
                'label' => 'form.name.label',
                'help' => 'minimum 8 caractéres'
            ])
            ->add('associationName', null, [
                'label' => 'form.associationName.label',
            ])
            ->add('description', null, [
                'label' => 'form.shortDescription.label',
                'help' => 'minimum 100 caractéres'
            ])
            ->add('text', CKEditorType::class, [
                'label' => 'form.description.label',
            ])
            ->add('domain',EntityType::class,[
                'class' => Domain::class,
                'choice_label' => $label,
                'multiple'=>true,
                'label' => 'form.domain.label',
            ])
            ->add('useOfFundsCollecteds', CollectionType::class, [
                "entry_type" => UseFundType::class,
                "by_reference" => false,
                "allow_delete" => true,
                "allow_add" => true,
                'label' => 'form.useOfFundsCollecteds.label',
            ])
            ->add('city', EntityType::class, [
                'class' => City::class,
                'label' => 'form.city.label',
                'placeholder' => 'form.city.placeholder'
            ])
            ->add('duration', ChoiceType::class, [
                'label' => 'form.duration.label',
                'placeholder' => 'form.duration.placeholder',
                'choices' => [
                    1 => 1,
                    2 => 2,
                    3 => 3,
                    4 => 4,
                    5 => 5,
                    6 => 6
                ]
            ])
            ->add('fundingObjective', null, [
                'attr' => [
                    'min' => 0,
                    'step' => 10000
                ],
                'label' => 'form.fundingObjective.label',
            ])
            ->add('RIB', null, [
                'label' => 'form.RIB.label',
            ])
            ->add('webSite', null, [
                'label' => 'form.webSite.label',
            ])
            ->end()
            ->end()
            ->tab('Images')
            ->with('Images', ['translation_domain' => 'company'])
            ->add('logoFile', VichImageType::class, [
                'required' => false,
                'label' => 'form.logoFile.label',
            ])
            ->add('coverFile', VichImageType::class, [
                'required' => false,
                'label' => 'form.coverFile.label',
            ])
            ->end()
            ->end()
            ->tab('Documents')
            ->with('Documents', ['translation_domain' => 'company'])
            ->add('documents', CollectionType::class, [
                "entry_type" => CompanyDocumentType::class,
                "by_reference" => false,
                "allow_delete" => true,
                "allow_add" => true,
                'label' => 'form.documents.label',
            ])
            ->end()
            ->end()
            ->tab('SEO')
            ->with('SEO', ['translation_domain' => 'company'])
            ->add('metaTitle', null, [
                'label' => 'form.metaTitle.label',
                'required' => false,
            ])
            ->add('metaDescription', TextareaType::class, [
                'label' => 'form.metaDescription.label',
                'required' => false
            ])
            ->end()
            ->end()
        ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add('preview', $this->getRouterIdParameter().'/preview')
            ->add('lock', $this->getRouterIdParameter().'/lock')
            ->add('reject', $this->getRouterIdParameter().'/reject')
            ->add('approve', $this->getRouterIdParameter().'/approve')
            ->add('verify', $this->getRouterIdParameter().'/verify')
            ->add('compare', $this->getRouterIdParameter().'/compare')
            ->add('reject_update', $this->getRouterIdParameter().'/reject-update')
            ->add('comments', $this->getRouterIdParameter().'/comments')
            //->add('contributors', $this->getRouterIdParameter().'/contributors')
        ;
    }

}
