<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Vich\UploaderBundle\Form\Type\VichImageType;

class AdminProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', null, [
               'label' => 'form.firstName.label'
            ])
            ->add('lastName', null, [
               'label' => 'form.lastName.label'
            ])
            ->add('email', null, [
               'label' => 'form.email.label'
            ])
            ->add('birthday', DateType::class, [
                'widget' => 'single_text',
               'label' => 'form.birthday.label'
            ])
            ->add('imageFile', VichImageType::class, [
                'label' => 'Avatar',
                'delete_label' => ' Delete',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'translation_domain' => 'admin',
        ]);
    }
}
