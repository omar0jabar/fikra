<?php

namespace EgioDigital\CMSBundle\Form;

use EgioDigital\CMSBundle\Entity\Block;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class BlockType extends AbstractType
{
   public function buildForm(FormBuilderInterface $builder, array $options)
   {
      $obj = $builder->getData();
      $required = false;
      $cols = [
         '1/1' => 12,
         '3/4' => 9,
         '2/3' => 8,
         '1/2' => 6,
         '1/3' => 4,
         '1/4' => 3,
      ];

      $builder
         ->add('position', TextType::class, ['required' => $required])
         ->add('row', TextType::class, ['required' => $required])
         ->add('type', TextType::class, ['required' => $required])
         ->add('colLarge', ChoiceType::class, ['choices'  => $cols])
         ->add('title', TextType::class, ['required' => $required])
         ->add('width', TextType::class, ['required' => $required])
         ->add('height', TextType::class, ['required' => $required])
         ->add('linkVideo', TextType::class, ['required' => $required])
          ->add('uploadImage', VichImageType::class, ['required' => false])
          ->add('alt')
          ->add('legend')
          ->add('linkImage')
          ->add('textImage', CKEditorType::class)
         ->add('content', CKEditorType::class, ['required' => $required])
         ->add('clearfix', null, [
             'label' => "Retour à la ligne"
         ])
      ;
   }

   public function configureOptions(OptionsResolver $resolver)
   {
      $resolver->setDefaults([
         'data_class' => Block::class,
      ]);
   }
}