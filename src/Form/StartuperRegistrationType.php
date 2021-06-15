<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\User;
use App\Helper\DataHelper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class StartuperRegistrationType extends AbstractType
{
    /**
     * @var DataHelper
     */
    private $dataHelper;

    /**
     * StartuperRegistrationType constructor.
     *
     * @param DataHelper $dataHelper
     */
    public function __construct(DataHelper $dataHelper)
    {
        $this->dataHelper =$dataHelper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('profile', ChoiceType::class, [
                'label' => false,
                'expanded' => true,
                'choices' => [
                    'form.profile.startup' => 'startuper',
                    'form.profile.investor' => 'investor',
                    'form.profile.bearerOfAnAssociativeProject' => 'bearer-of-an-associative-project',
                ]
            ])
            ->add('firstName', null, [
                'label' => 'form.firstName.label',
                'attr' => [
                    'placeholder' => 'form.firstName.placeholder'
                ]
            ])
            ->add('lastName', null, [
                'label' => 'form.lastName.label',
                'attr' => [
                    'placeholder' => 'form.lastName.placeholder'
                ]
            ])
            ->add('city', EntityType::class, [
                'class' => City::class,
                'label' => 'form.city.label',
                'placeholder' => 'form.city.placeholder',
                'required' => false,
            ])
            ->add('socialReason', null, [
                'label' => 'form.socialReason.label',
                'required' => false,
                'attr' => [
                    'placeholder' => 'form.socialReason.placeholder'
                ]
            ])
            ->add('phone', null, [
                'label' => 'form.phone.label',
                'attr' => [
                    'placeholder' => 'form.phone.placeholder'
                ]
            ])
            ->add('email', null, [
                'label' => 'form.email.label',
                'attr' => [
                    'placeholder' => 'form.email.placeholder'
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => ['label' => 'form.password.label',
                    'attr' => [
                        'placeholder' => 'form.password.placeholder'
                    ]],
                'second_options' => ['label' => 'form.confirmPassword.label',
                    'attr' => [
                        'placeholder' => 'form.confirmPassword.placeholder'
                    ]],
            ])
            ->add('imageFile', VichImageType::class, [
                'label' => false,
                'delete_label' => ' Delete',
                'required' => false,
                'attr' => [
                    'onchange' => "showIMG(this);"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'translation_domain' => 'startuper',
            'attr' => [
                'id' => 'registration-form',
                //'class' => 'recaptcha-form'
            ]
        ]);
    }

}
