<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Company;
use App\Entity\Domain;
use App\Helper\DataHelper;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * Class CompanyType
 * @package App\Form
 */
class CompanyType extends AbstractType
{

    /**
     * @var DataHelper
     */
    private $dataHelper;

    /**
     * CompanyType constructor.
     *
     * @param DataHelper $dataHelper
     */
    public function __construct(
        DataHelper $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locale = \Locale::getDefault();
        $label = 'labelFr';
        if ($locale == 'en') {
            $label = 'labelEn';
        }
        $builder
            ->add('name', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'form.name.placeholder'
                ]
            ])
            ->add('associationName', TextType::class, [
                'label' => 'form.associationName.label',
                'attr' => [
                    'placeholder' => 'form.associationName.placeholder'
                ]
            ])
            ->add('domain', EntityType::class, [
                'class' => domain::class,
                'choice_label' => $label,
                'multiple'=>true,
                'label' => 'form.domain.label',
                'required' => false,
                'attr' => [
                    'class' => 'select2 custom-select-field',
                    'placeholder' => 'form.domain.placeholder'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'form.shortDescription.help'
                ]
            ])
            ->add('text', CKEditorType::class, [
                'config_name' => 'config_front',
                'label' => false,
                'attr' => [
                    'placeholder' => 'form.description.placeholder'
                ]
            ])
            ->add('city', EntityType::class, [
                'class' => City::class,
                'required' => false,
                'label' => 'form.city.label',
                'placeholder' => 'form.city.placeholder',
            ])
            ->add('duration', ChoiceType::class, [
                'label' => 'form.duration.label',
                'placeholder' => 'form.duration.placeholder',
                'choices' => $this->dataHelper->getDurations(),
            ])
            ->add('fundingObjective', MoneyType::class, [
                'currency' => 'MAD',
                'scale' => 0,
                'label' => false,
                'attr' => [
                    'placeholder' => 'form.fundingObjective.placeholder'
                ],
                'error_bubbling' => false,
            ])
            ->add('useOfFundsCollecteds', CollectionType::class, [
                "entry_type" => UseFundType::class,
                "allow_add" => true,
                "allow_delete" => true,
                'label' => false,
                'required' => false
            ])
            ->add('RIB', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'form.RIB.placeholder',
                    'minlength' => 24,
                    'maxlength' => 24
                ]
            ])
            ->add('webSite', TextType::class, [
                'required' => false,
                'label' => 'form.webSite.label',
                'attr' => [
                    'placeholder' => 'form.webSite.placeholder'
                ]
            ])
            ->add('logoFile', VichImageType::class, [
                'required' => false,
                'label' => 'form.logoFile.label',
                'help' => 'form.logoFile.help',
                'translation_domain' => 'company',
                'attr' => [
                    "onchange" => "showURL(this)",
                    'placeholder' => "form.logoFile.placeholder",
                    'required' => false
                ],
                'download_uri' => false,
                "allow_delete" => false,
            ])
            ->add('coverFile', VichImageType::class, [
                'required' => false,
                'label' => 'form.coverFile.label',
                'help' => 'form.coverFile.help',
                'translation_domain' => 'company',
                'attr' => [
                    "onchange" => "showURL(this)",
                    'placeholder' => "form.coverFile.placeholder",
                    'required' => false
                ],
                'download_uri' => false,
                "allow_delete" => false,
            ])
            ->add('documents', CollectionType::class, [
                "entry_type" => CompanyDocumentType::class,
                "allow_add" => true,
                "allow_delete" => true,
                'label' => 'form.documents.label',
                'required' => false
            ])
            ->add('isLegalRepresentativeOfTheAssociation', null, [
                'required' => true,
                'label' => 'form.isLegalRepresentativeOfTheAssociation.label',
            ])
            ->add('isAcceptedTheConditionOfSecurity', null, [
                'required' => true,
                'label' => 'form.isAcceptedTheConditionOfSecurity.label',
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Company::class,
            'translation_domain' => 'company',
        ]);
    }

}
