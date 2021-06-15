<?php

namespace App\Form;

use App\Entity\User;
use App\Repository\RoleRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('phone')
            ->add('email')
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'required' => true,
                'first_options' => array('label' => 'Password'),
                'second_options' => array('label' => 'Password confirmation'),

            ])
            ->add('city')
            ->add('birthday', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('role', null, [
               'query_builder' => function (RoleRepository $role) {
                  return $role->createQueryBuilder('r')
                     ->where('r.label != :role')
                     ->setParameter('role', "ROLE_STARTUPER");
               },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
           'translation_domain' => 'user',
        ]);
    }
}
