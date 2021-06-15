<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('currentPassword', PasswordType::class, [
               'label' => 'form.currentPassword.label',
               'attr' => [
                  'minLength' => 6,
                  'placeholder' => 'form.currentPassword.placeholder'
               ]
            ])
            ->add('newPassword', PasswordType::class, [
               'label' => 'form.newPassword.label',
                'attr' => [
                    'minLength' => 6,
                   'placeholder' => 'form.newPassword.placeholder'
                ]
            ])
            ->add('confirmPassword', PasswordType::class, [
               'label' => 'form.confirmPassword.label',
               'attr' => [
                  'minLength' => 6,
                  'placeholder' => 'form.confirmPassword.placeholder'
               ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
           'translation_domain' => 'startuper',
            'attr' => [
                'id' => 'change-password-form'
            ]
        ]);
    }
}
