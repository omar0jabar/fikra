<?php

namespace App\Form;

use App\Entity\Document;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class DocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locale = \Locale::getDefault();
        $label = 'labelFr';
        if ($locale == 'en') {
            $label = 'labelEn';
        }
        $builder
            ->add('file', VichFileType::class, [
                'translation_domain' => 'projectDocument',
                'label' => false,
                'attr' => [
                    'onchange' => "showURL(this)",
                    'placeholder' => "form.file.placeholder",
                    'required' => false
                ],
                'download_uri' => false,
                'allow_delete' => false,
            ])
            ->add('documentType', EntityType::class, [
                'label' => false,
                'class' => \App\Entity\DocumentType::class,
                'choice_label' => $label,
                'placeholder' => 'form.document_type.placeholder',
                'attr' => [
                    'required' => true
                ],
            ])
            ->add("isPrivate", ChoiceType::class, [
                'label' => false,
                'choices' => [
                    'Confidential document' => true,
                    'Public document' => false,
                ],
                'multiple'=>false,
                'expanded'=>true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Document::class,
            'translation_domain' => 'projectDocument',
        ]);
    }
}
