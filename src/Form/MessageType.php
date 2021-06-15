<?php

namespace App\Form;

use App\Entity\Message;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('object', null, [
                'label' => 'form.object.label',
                'attr' => [
                    'placeholder' => 'form.object.placeholder'
                ]
            ])
            ->add('content', null, [
                'label' => 'form.content.label',
                'attr' => [
                    'placeholder' => 'form.content.placeholder'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
            'translation_domain' => 'message',
            'attr' => [
                'id' => 'form-message'
            ]
        ]);
    }
}
