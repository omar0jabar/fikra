<?php

namespace App\Form;

use App\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ChangeCoverProjectType extends AbstractType
{
   public function buildForm(FormBuilderInterface $builder, array $options)
   {
      $builder
         ->add('imageFile', VichImageType::class, [
            'label' => 'form.cover.label',
            'attr' => [
               'placeholder' => 'form.cover.placeholder',
               'onchange' => 'showIMG(this)'
            ],
            'help' => 'form.cover.help',
            'allow_delete' => false,
            'download_link' => false,
            'image_uri' => false,
            'translation_domain' => 'project',
         ])
      ;
   }

   public function configureOptions(OptionsResolver $resolver)
   {
      $resolver->setDefaults([
         'data_class' => Project::class,
         'translation_domain' => 'project',
         'validation_groups' => "cover"
      ]);
   }
}
