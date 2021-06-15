<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'form.firstName.label',
                'attr' => [
                    'placeholder' => 'form.firstName.placeholder',
                ]
            ])
            ->add('lastName', TextType::class, [
                'label' => 'form.lastName.label',
                'attr' => [
                    'placeholder' => 'form.lastName.placeholder'
                ]
            ])
            ->add('email', TextType::class, [
                'label' => 'form.email.label',
                'attr' => [
                    'placeholder' => 'form.email.placeholder'
                ]
            ])
            ->add('phone', TextType::class, [
                'label' => 'form.phone.label',
                'attr' => [
                    'placeholder' => 'form.phone.placeholder'
                ],
                'required' => false
            ])
            ->add('object', TextType::class, [
                'label' => 'form.object.label',
                'attr' => [
                    'placeholder' => 'form.object.placeholder'
                ]
            ])
            ->add('message', TextareaType::class, [
                'label' => 'form.message.label',
                'attr' => [
                    'placeholder' => 'form.message.placeholder'
                ]
            ])
        ;
//        $locale = \Locale::getDefault();
//        $builder->add('recaptcha', EWZRecaptchaType::class, array(
//            'required' => false,
//            'label' => false,
//            'attr' => [
//                'options' => [
//                    'theme' => 'light',
//                    'type'  => 'image',
//                    'size' => 'invisible',              // set size to invisible
//                    'defer' => true,
//                    'async' => true,
//                    'callback' => 'onReCaptchaSuccess', // callback will be set by default if not defined (along with JS function that validate the form on success)
//                    'language' => $locale
//                ]
//            ],
//            //'mapped' => false,
//            //'error_bubbling' => true,
//            /*'constraints' => [
//                new RecaptchaTrue()
//            ]*/
//        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
            'translation_domain' => 'contact',
            'attr' => [
                'id' => 'contact-form',
                //'class' => 'recaptcha-form'
            ]
        ]);
    }
}
