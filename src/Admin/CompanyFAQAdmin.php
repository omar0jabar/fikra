<?php

namespace App\Admin;

use App\Helper\DataHelper;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Filter\StringFilter;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class CompanyFAQAdmin
 * @package App\Admin
 */
final class CompanyFAQAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'sonata_admin_company_faq';
    protected $baseRoutePattern = 'campaign-faq';
    protected $classnameLabel = 'FAQ';
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
     * CompanyFAQAdmin constructor.
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
        parent::__construct($code, $class, $baseControllerName);
        $this->dataHelper = $dataHelper;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('question', null, [
                'label' => 'form.question.label'
            ])
            ->add('response', CKEditorType::class, [
                'label' => 'form.response.label'
            ])
            ->add('language', ChoiceType::class, [
                'label' => 'form.language.label',
                'choices' => $this->dataHelper->getLanguages()
            ])
            ->add('isPublished', null, [
                'label' => 'form.isPublished.label'
            ]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('question', null, [
                'label' => 'form.question.label'
            ])
            ->add('language',
                StringFilter::class,
                ['label' => 'form.language.label'],
                ChoiceType::class,
                ['choices' => $this->dataHelper->getLanguages()]
            )
            ->add('isPublished', null, [
                'label' => 'form.isPublished.label'
            ]);
    }

    protected function configureListFields(ListMapper $listMapper)
    {

        $listMapper
            ->addIdentifier('id')
            ->add('question', null, [
                'label' => 'form.question.label'
            ])
            ->add('isPublished', null, [
                'label' => 'form.isPublished.label'
            ])
            ->add('language', ChoiceType::class, [
                'label' => 'form.language.label',
                'choices' => $this->dataHelper->getLanguages()
            ])
            ->add('createdAt', null, [
                'label' => 'form.createdAt.label'
            ])
            ->add('updatedAt', null, [
                'label' => 'form.updatedAt.label'
            ])
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => []
                ]
            ]);
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('question', null, [
                'label' => 'form.question.label'
            ])
            ->add('response', null, [
                'label' => 'form.response.label',
                'template' => 'bundles/SonataAdmin/CRUD/show_wysiwyg.html.twig'
            ])
            ->add('language', ChoiceType::class, [
                'label' => 'form.language.label',
                'choices' => $this->dataHelper->getLanguages()
            ])
            ->add('isPublished', null, [
                'label' => 'form.isPublished.label'
            ])
            ->add('createdAt', null, [
                'label' => 'form.createdAt.label'
            ])
            ->add('updatedAt', null, [
                'label' => 'form.updatedAt.label'
            ]);
    }

}