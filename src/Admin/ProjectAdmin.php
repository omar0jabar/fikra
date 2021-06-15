<?php

namespace App\Admin;

use App\Entity\BusinessModel;
use App\Entity\Earned;
use App\Entity\SalesChannels;
use App\Entity\Sector;
use App\Entity\Step;
use App\Entity\User;
use App\Form\AvantageType;
use App\Form\DocumentType;
use App\Form\GalleryPhotoType;
use App\Form\ProjectFinanceType;
use App\Form\ServiceType;
use App\Form\TeamMemberType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Vich\UploaderBundle\Form\Type\VichImageType;

final class ProjectAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'sonata_admin_project';
    protected $baseRoutePattern = 'project';
    protected $classnameLabel = 'Project';
    protected $translationDomain = 'project';
    protected $datagridValues = [
        '_sort_order' => 'DESC',
        '_sort_by' => 'id',
    ];

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
            ->add('author', null, [
                'class' => User::class,
                'property' => 'firstName',
                'admin_code' => 'sonata.admin.startuper',
            ])
            ->add('sectors', null, [
                'label' => 'form.sectors.label'
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
            ->add('orderBy', null, [
                'label' => 'Order'
            ])
            ->addIdentifier('name', null, [
                'label' => 'form.name.label'
            ])
            ->add('author', EntityType::class, [
                'class' => User::class,
                'property' => 'firstName',
                'admin_code' => 'sonata.admin.startuper',
                'label' => 'form.author.label'
            ])
            ->add('isDraft', null, [
                'template' => 'bundles/SonataAdmin/CRUD/list_draft.html.twig',
                'label' => 'form.isDraft.label'
            ])
            ->add('isVerified', null, [
                'template' => 'bundles/SonataAdmin/CRUD/list_verified.html.twig',
                'label' => 'form.isVerified.label'
            ])
            ->add('isApproved', null, [
                'label' => 'form.isApproved.label'
            ])
            ->add('isUpdated', null, [
                'template' => 'bundles/SonataAdmin/CRUD/list_updated.html.twig',
                'label' => 'form.isUpdated.label'
            ])
            ->add('isRejected', null, [
                'template' => 'bundles/SonataAdmin/CRUD/list_rejected.html.twig',
                'label' => 'form.isRejected.label'
            ])
            ->add('createdAt')
            ->add('updatedAt')
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
                    'view' => [
                        'template' => 'bundles/SonataAdmin/CRUD/list__action_compare.html.twig',
                    ],
                    'messages' => [
                        'template' => 'bundles/SonataAdmin/CRUD/list__action_messages.html.twig',
                    ],
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
            
            ->tab('L\'idée')
            ->with('L\'idée', ['translation_domain' => 'project'])
            ->add('orderBy', null, [
                'label' => 'Order'
            ])
            ->add('name', null, [
                'label' => 'form.name.label',
                'help' => 'minimum 8 caractéres'
            ])
            ->add('description', null, [
                'label' => 'form.description.label',
                'help' => 'minimum 100 caractéres'
            ])
            ->add('sectors',EntityType::class,[
                'class' => Sector::class,
                'choice_label' => $label,
                'multiple'=>true,
                'label' => 'form.sectors.label',
            ])
            ->add('moreSectors', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Décrivez ...'
                ],
                'required' => false
            ])
            ->add('summary', CKEditorType::class, [
                //'config_name' => 'config_front',
                'label' => 'form.summary.label',
            ])
            ->add('step',EntityType::class,[
                'class' => Step::class,
                'choice_label' => $label,
                'multiple'=>false,
                'expanded'=>true,
                'label' => 'form.step.label',
            ])
            ->add('earned',EntityType::class,[
                'class' => Earned::class,
                'choice_label' => $label,
                'label' => 'form.earned.label',
            ])
            ->add('services', CollectionType::class, [
                "entry_type" => ServiceType::class,
                "by_reference" => false,
                "allow_delete" => true,
                "allow_add" => true,
                'label' => 'form.mainProducts.label',
            ])
            ->add('startup', null, [
                'label' => 'form.startup.label',
            ])
            ->add('denomination', null, [
                'label' => 'form.denomination.label',
            ])
            ->add('creatingDate', DateType::class, [
                'widget' => 'single_text',
                'label' => 'form.creatingDate.label',
                'required' => false
            ])
            ->add('rc', null, [
                'label' => 'form.rc.label',
            ])
            ->add('city', null, [
                'label' => 'form.city.label',
            ])
            ->end()
            ->end()

            

            ->tab('Le projet')
            ->with('Le projet', ['translation_domain' => 'project'])
            ->add('salesChannels', EntityType::class, [
                'class' => SalesChannels::class,
                'choice_label' => $label,
                'multiple' => true,
                'expanded'=>true,
                'label' => 'form.salesChannels.label',
            ])
            ->add('otherSalesChannels', null, [
                'attr' => [
                ],
            ])
            ->add('moreSalesChannels', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Décrivez ...'
                ],
                'required' => false
            ])
            ->add('businessModels', EntityType::class, [
                'class' => BusinessModel::class,
                'choice_label' => $label,
                'attr' => [
                ],
                'multiple'=>true,
                'expanded'=>true,
                'required' => false,
                'placeholder' => null,
                'label' => 'form.businessModels.label',
            ])
            ->add('otherBusinessModel', null, [
                'attr' => [
                ],
                'required' => false
            ])
            ->add('moreBusinessModel', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Décrivez ...'
                ],
                'required' => false
            ])
            ->add('avantages', CollectionType::class, [
                "entry_type" => AvantageType::class,
                "by_reference" => false,
                "allow_delete" => true,
                "allow_add" => true,
                'label' => 'form.avantages.label',
            ])
            ->add('hasNotAmount', null, [
                'label' => 'form.hasNotAmount.label',
            ])
            ->add('raised', null, [
                'attr' => [
                    'min' => 0,
                    'step' => 10000
                ],
                'label' => 'form.raised.label',
            ])
            ->add('amount', null, [
                'attr' => [
                    'min' => 0,
                    'step' => 10000
                ],
                'label' => 'form.amount.label',
            ])
            ->add('projectFinances', CollectionType::class, [
                "entry_type" => ProjectFinanceType::class,
                "by_reference" => false,
                "allow_delete" => true,
                "allow_add" => true,
                'label' => 'form.projectFinances',
            ])
            ->add('express', CKEditorType::class, [
                //'config_name' => 'config_front',
                'label' => 'form.express.label',
            ])
            ->end()
            ->end()

            ->tab('L\'équipe')
            ->with('L\'équipe')
            ->add('teamMembers', CollectionType::class, [
                "entry_type" => TeamMemberType::class,
                "by_reference" => false,
                "allow_delete" => true,
                "allow_add" => true,
                "required" => false
            ])
            ->end()
            ->end()
            ->tab('Documents')
            ->with('')
            ->add('documents', CollectionType::class, [
                "entry_type" => DocumentType::class,
                "by_reference" => false,
                "allow_delete" => true,
                "allow_add" => true,
                "required" => false
            ])
            ->end()
            ->end()

            ->tab('Gallery photos')
            ->with('')
            ->add('galleryPhotos', CollectionType::class, [
                "entry_type" => GalleryPhotoType::class,
                "by_reference" => false,
                "allow_delete" => true,
                "allow_add" => true,
                "required" => false
            ])
            ->end()
            ->end()

            ->tab('Media')
            ->add('imageFile', VichImageType::class, [
                'required' => false,
            ])
            ->add('logoFile', VichImageType::class, [
                'required' => false,
            ])
            ->add('video', null, [
                'required' => false,
            ])
            ->end()
            ->end()

            ->tab('Bloc HTML')
            ->with('Bloc HTML', ['translation_domain' => 'project'])
                ->add('metaTitle', null, [
                    'label' => 'form.metaTitle.label',
                ])
                ->add('metaDescription', null, [
                    'label' => 'form.metaDescription.label',
                ])
            ->end()
            ->end()
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->tab('L\'idée')
            ->with('')
            ->add('name', null, [
                'label' => 'form.name.label'
            ])
            ->add('description', null, [
                'label' => 'form.description.label'
            ])
            ->add('step', null, [
                'label' => 'form.step.label'
            ])
            ->add('earned', null, [
                'label' => 'form.earned.label'
            ])
            ->add('mainProducts', null, [
                'label' => 'form.mainProducts.label'
            ])
            ->add('startup', null, [
                'label' => 'form.startup.label'
            ])
            ->add('denomination', null, [
                'label' => 'form.denomination.label'
            ])
            ->add('creatingDate', null, [
                'label' => 'form.creatingDate.label'
            ])
            ->add('rc', null, [
                'label' => 'form.rc.label'
            ])
            ->add('city', null, [
                'label' => 'form.city.label'
            ])
            ->end()
            ->end()
            ->tab('Le projet')
            ->with('')
            ->add('salesChannels')
            ->add('sectors')
            ->add('businessModels')
            ->add('morocco')
            ->add('otherCountry')
            ->add('foreignCountry')
            ->add('avantages')
            ->add('budget')
            ->add('raised')
            ->add('amount')
            ->add('projectFinances')
            ->add('summary')
            ->add('express')
            ->end()
            ->end()
            ->tab('L\'équipe')
            ->with('')
            ->add('teamMembers', CollectionType::class, [
                'template' => 'bundles/SonataAdmin/CRUD/show_members.html.twig',
            ])
            ->end()
            ->end()
            ->tab('Documents')
            ->with('')
            ->add('documents', CollectionType::class, [
                'template' => 'bundles/SonataAdmin/CRUD/show_documents.html.twig',
            ])
            ->end()
            ->end()
            ->tab('Gallery Photos')
            ->with('')
            ->add('galleryPhotos', CollectionType::class, [
                'template' => 'bundles/SonataAdmin/CRUD/show_gallery.html.twig',
            ])
            ->end()
            ->end()
            ->tab('Media')
            ->add('cover', VichImageType::class)
            ->add('logo', VichImageType::class)
            ->add('video')
            ->end()
        ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create')
            ->add('preview', $this->getRouterIdParameter().'/preview')
            ->add('approve', $this->getRouterIdParameter().'/approve')
            ->add('lock', $this->getRouterIdParameter().'/lock')
            ->add('compare', $this->getRouterIdParameter().'/compare')
            ->add('verify', $this->getRouterIdParameter().'/verify')
            ->add('reject', $this->getRouterIdParameter().'/reject')
            ->add('reject_update', $this->getRouterIdParameter().'/reject-update')
            ->add('messages', $this->getRouterIdParameter().'/messages');
    }

}
