<?php

namespace App\Form;

use App\Entity\TeamMember;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class TeamMemberType extends AbstractType
{
   public function buildForm(FormBuilderInterface $builder, array $options)
   {
      $builder
         ->add('firstName', TextType::class, [
            'attr' => [
               'placeholder' => 'form.firstName.placeholder'
            ],
            'label' => 'form.firstName.label'
         ])
         ->add('lastName', TextType::class, [
            'attr' => [
               'placeholder' => 'form.lastName.placeholder'
            ],
            'label' => 'form.lastName.label'
         ])
         ->add('porteur', null, [
            'label' => 'form.porteur.label',
            'attr' => [
               'onclick' => 'deselectPorteurs(this)'
            ]
         ])
         ->add('imageFile', VichImageType::class, [
            'translation_domain' => 'teamMember',
            //'label' => 'form.avatar.label',
            'label' => false,
            'attr' => [
               'onchange' => 'showIMGMember(this)',
               'placeholder' => "form.avatar.placeholder"
            ],
            'download_uri' => false,
            'allow_delete' => false,
            'required' => false
         ])
         ->add('position', TextType::class, [
            'attr' => [
               'placeholder' => 'form.position.placeholder'
            ],
            'label' => 'form.position.label'
         ])
         ->add('biography', TextareaType::class, [
            'attr' => [
               'placeholder' => 'form.biography.placeholder',
               'rows' => 4
            ],
            'required' => false,
            'label' => false
         ])
         ->add('cvFile', VichImageType::class, [
            'translation_domain' => 'teamMember',
            'label' => false,
            'attr' => [
               'onchange' => 'showURL(this)',
               'placeholder' => "form.cv.placeholder"
            ],
            'download_uri' => false,
            'allow_delete' => false,
            'image_uri' => false,
            'required' => false,
         ])
         ->add('linkedin', TextType::class, [
            'attr' => [
               'placeholder' => 'LinkedIn ...'
            ],
            'required' => false
         ])
         ->add('twitter', TextType::class, [
            'attr' => [
               'placeholder' => 'Twitter ...'
            ],
            'required' => false
         ])
         ->add('facebook', TextType::class, [
            'attr' => [
               'placeholder' => 'Facebook ...'
            ],
            'required' => false
         ])
      ;
   }

   public function configureOptions(OptionsResolver $resolver)
   {
      $resolver->setDefaults([
         'data_class' => TeamMember::class,
         'translation_domain' => 'teamMember',
      ]);
   }
}
