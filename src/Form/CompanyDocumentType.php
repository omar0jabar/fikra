<?php

namespace App\Form;

use App\Entity\CompanyDocument;
use App\Helper\DataHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

/**
 * Class CompanyDocumentType
 * @package App\Form
 */
class CompanyDocumentType extends AbstractType
{
    /**
     * @var DataHelper
     */
    private $dataHelper;

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
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => $this->dataHelper->getCompanyDocumentTypes(),
            ])
            ->add('file', VichFileType::class, [
                'required' => false,
                'label' => false,
                'translation_domain' => 'company',
                'attr' => [
                    "onchange" => "showURL(this)",
                    'placeholder' => "form.documentFile.placeholder",
                    'required' => false
                ],
                'download_uri' => false,
                "allow_delete" => false,
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CompanyDocument::class,
            'translation_domain' => 'company',
        ]);
    }
}
