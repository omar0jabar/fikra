<?php

namespace App\Form;

use App\Entity\BusinessModel;
use App\Entity\Earned;
use App\Entity\Project;
use App\Entity\SalesChannels;
use App\Entity\Sector;
use App\Entity\Step;
use App\Repository\SectorRepository;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Intl\Locale\Locale;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $obj = $builder->getData();
        $project = $obj->getId() === null ? false : true;
        $locale = \Locale::getDefault();
        $label = 'labelFr';
        if ($locale == 'en') {
            $label = 'labelEn';
        }

        $builder
            ->add('saveWithoutSkip1', SubmitType::class, [
                'label' => 'form.save.label',
                'attr' => [
                    'class' => 'btn btn-bg-red btn-h50 btn-w35'
                ]
            ])
            ->add('saveWithoutSkip2', SubmitType::class, [
                'label' => 'form.save.label',
                'attr' => [
                    'class' => 'btn btn-bg-red btn-h50 btn-w35'
                ]
            ])
        ;
        switch ($options['flow_step']) {
            case 1:
                /*
                if ($project === false) {
                    $builder
                        ->add('language', ChoiceType::class, [
                            'label' => 'form.language.label',
                            'choices' => [
                                'form.language.french' => 'fr',
                                'form.language.english' => 'en',
                            ],
                            'attr' => [
                                'class' => 'select2',
                            ],
                            'data' => $locale,
                        ]);
                }
                */
                $builder
                    ->add('name', TextType::class, [
                        'label' => 'form.name.label',
                        'attr' => [
                            'placeholder' => 'form.name.placeholder',
                        ]
                    ])
                    ->add('description', TextType::class, [
                        'label' => 'form.description.label',
                        'attr' => [
                            'placeholder' => 'form.description.placeholder',
                        ]
                    ])
                    ->add('sectors', EntityType::class, [
                        'class' => Sector::class,
                        'choice_label' => $label,
                        'multiple'=>true,
                        'label' => 'form.sectors.label',
                        'attr' => [
                            'class' => 'select2 custom-select-field',
                            'placeholder' => 'form.sectors.placeholder'
                        ],
                        'query_builder' => function(SectorRepository $repo) {
                            return $repo->getByOrderLabelAsc();
                        }
                    ])
                    ->add('moreSectors', TextType::class, [
                        'label' => "form.moreSectors.label",
                        'attr' => [
                            'placeholder' => 'form.moreSectors.placeholder',
                        ],
                        'required' => false
                    ])
                    ->add('summary', CKEditorType::class, [
                        'config_name' => 'config_front',
                        'label' => 'form.summary.label',
                        'attr' => [
                            'placeholder' => 'form.summary.placeholder',
                        ],
                        'required' => true
                    ]);
                $builder
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
                        'label' => false,
                        'attr' => [
                            'class' => 'select2',
                        ]
                    ])
                    ->add('services', CollectionType::class, [
                        "entry_type" => ServiceType::class,
                        "allow_add" => true,
                        "allow_delete" => true,
                        'label' => 'form.mainProducts.label',
                        'required' => false
                    ])
                    ->add('startup', ChoiceType::class, [
                        'label' => 'do you have a startup',
                        'choices' => [
                            'form.marketResearch.yes' => true,
                            'form.marketResearch.no' => false,
                        ],
                        'multiple'=>false,
                        'expanded'=>true,
                        /*'attr' => [
                           'onclick' => 'showInfoStartup()',
                        ],*/
                    ])
                    ->add('denomination', null, [
                        'label' => false,
                        'attr' => [
                            'placeholder' => 'form.denomination.placeholder',
                        ]
                    ])
                    ->add('creatingDate', DateType::class, [
                        'widget' => 'single_text',
                        'label' => false,
                    ])
                    ->add('rc', null, [
                        'label' => false,
                        'attr' => [
                            'placeholder' => 'form.rc.placeholder',
                        ]
                    ])
                    ->add('city', null, [
                        'label' => false,
                        'attr' => [
                            'placeholder' => 'form.city.placeholder',
                        ]
                    ])
                ;
                break;
            case 2:
                $builder
                    ->add('salesChannels', EntityType::class, [
                        'class' => SalesChannels::class,
                        'choice_label' => $label,
                        'multiple' => true,
                        'expanded'=>true,
                        'label' => 'form.salesChannels.label',
                        'help' => 'form.salesChannels.help',
                    ])
                    ->add('otherSalesChannels', null, [
                        'label' => 'form.otherSalesChannels.label',
                        'attr' => [
                            'onchange' => 'showOrHideAnotherSalesChannels()',
                        ],
                    ])
                    ->add('moreSalesChannels', TextType::class, [
                        'label' => false,
                        'attr' => [
                            'placeholder' => 'form.moreSalesChannels.placeholder',
                        ],
                        'required' => false
                    ]);
                $builder->add('businessModels', EntityType::class, [
                        'class' => BusinessModel::class,
                        'choice_label' => $label,
                        'multiple'=>true,
                        'expanded'=>true,
                        'label' => 'form.businessModels.label',
                    ])
                    ->add('otherBusinessModel', null, [
                        'label' => 'form.otherBusinessModels.label',
                        'attr' => [
                            'onchange' => "showOrHideAnotherBusinessModel()",
                        ]
                    ])
                    ->add('moreBusinessModel', TextType::class, [
                        'label' => false,
                        'attr' => [
                            'placeholder' => 'form.moreBusinessModel.placeholder',
                        ],
                        'required' => false
                    ])

                    /*->add('morocco', null, [
                       'label' => 'form.morocco.label'
                    ])
                    ->add('otherCountry', null, [
                       'label' => 'form.otherCountry.label',
                       'attr' => [
                          'onchange' => 'showOrHideForeignCountry()',
                       ]
                    ])
                    ->add('foreignCountry', TextType::class, [
                       'label' => false,
                       'attr' => [
                          'placeholder' => 'form.foreignCountry.placeholder',
                       ],
                       'required' => false,
                    ])*/

                    /*->add('marketResearch', ChoiceType::class, [
                       'choices' => [
                          'form.marketResearch.yes' => true,
                          'form.marketResearch.no' => false,
                       ],
                       'multiple'=>false,
                       'expanded'=>true,
                       'label' => 'form.marketResearch.label',
                    ])*/
                    ->add('avantages', CollectionType::class, [
                        "entry_type" => AvantageType::class,
                        "allow_add" => true,
                        "allow_delete" => true,
                        'label' => 'form.avantages.label',
                        'required' => false
                    ])
                    /*->add('budget', null, [
                       'attr' => [
                          'placeholder' => 'Budget ...',
                          'min' => 0,
                          'max' => 1000000000,
                          'step' => 10000,
                          'onchange' => 'countAmount()',
                       ],
                       'label' => 'form.budget',
                    ])*/
                    ->add('raised', null, [
                        'attr' => [
                            'placeholder' => 'form.raised.placeholder',
                            'min' => 0,
                            'max' => 1000000000,
                            'step' => 10000,
                            //'onchange' => 'countAmount()',
                        ],
                        'label' => 'form.raised.label',
                    ])
                    ->add('hasNotAmount', null, [
                        'label' => 'form.hasNotAmount.label',
                    ])
                    ->add('amount', null, [
                        'attr' => [
                            'placeholder' => 'form.amount.placeholder',
                            'min' => 0,
                            'max' => 1000000000,
                            'step' => 10000,
                            //'onchange' => 'limitAmount()',
                        ],
                        'label' => 'form.amount.label',
                    ])
                    ->add('projectFinances', CollectionType::class, [
                        "entry_type" => ProjectFinanceType::class,
                        "allow_add" => true,
                        "allow_delete" => true,
                        'label' => 'form.projectFinances',
                        'required' => false
                    ])
                    ->add('express', CKEditorType::class, [
                        'label' => 'form.express.label',
                        'config_name' => 'config_front',
                        'attr' => [
                            'placeholder' => 'form.express.placeholder',
                        ]
                    ])
                ;
                break;
            case 3:
                $builder
                    ->add('teamMembers', CollectionType::class, [
                        'label' => false,
                        'entry_type' => TeamMemberType::class,
                        "allow_add" => true,
                        "allow_delete" => true,
                    ])
                ;
                break;
            case 4:
                $builder
                    ->add('documents', CollectionType::class, [
                        'entry_type' => DocumentType::class,
                        'label' => false,
                        "allow_add" => true,
                        "allow_delete" => true,
                        'required' => false,
                    ])
                ;
                break;
            case 5:
                $builder
                    ->add('galleryPhotos', CollectionType::class, [
                        'entry_type' => GalleryPhotoType::class,
                        'label' => false,
                        "allow_add" => true,
                        "allow_delete" => true,
                        'required' => false,
                    ])
                ;
                break;

            case 6:
                $builder
                    ->add('metaTitle', TextType::class, [
                        'label' => 'form.metaTitle.label',
                        'attr' => [
                            'placeholder' => 'form.metaTitle.placeholder',
                        ]
                    ])
                    ->add('metaDescription', TextType::class, [
                        'label' => 'form.metaDescription.label',
                        'attr' => [
                            'placeholder' => 'form.metaDescription.placeholder',
                        ]
                    ])
                ;
                break;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
            'translation_domain' => 'project',
            'attr' => [
                'id' => 'project-form'
            ]
        ]);
    }

    public function getBlockPrefix() {
        return 'project_form';
    }
}
