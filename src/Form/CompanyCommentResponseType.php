<?php

namespace App\Form;

use App\Entity\CompanyCommentResponse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CompanyCommentResponseType
 * @package App\Form
 */
class CompanyCommentResponseType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'form.comment.placeholder',
                    'rows' => "2"
                ]
            ])
            ->add('isPublished', null, [
                'label' => "form.isPublished.label",
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CompanyCommentResponse::class,
            'translation_domain' => 'company',
            'attr' => [
                'id' => 'form-company-comment'
            ]
        ]);
    }
}
