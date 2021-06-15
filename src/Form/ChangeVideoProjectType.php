<?php

namespace App\Form;

use App\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangeVideoProjectType extends AbstractType
{
   public function buildForm(FormBuilderInterface $builder, array $options)
   {
      $builder
         ->add('video', null, [
            'label' => 'form.video.label',
            'attr' => [
               'placeholder' => 'form.video.placeholder',
            ],
         ])
      ;
   }

   public function configureOptions(OptionsResolver $resolver)
   {
      $resolver->setDefaults([
         'data_class' => Project::class,
         'translation_domain' => 'project',
         'validation_groups' => "video"
      ]);
   }
}
