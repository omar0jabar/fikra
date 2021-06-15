<?php

namespace App\Form;

use App\Entity\MessageResponse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageResponseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', null, [
                'label' => 'form.content.label',
                'attr' => [
                    'placeholder' => 'form.content.placeholder'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MessageResponse::class,
            'translation_domain' => 'message',
            'attr' => [
                'id' => 'message-response-form'
            ]
        ]);
    }
}
