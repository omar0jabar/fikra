<?php

namespace App\Form;

use App\Entity\Avantage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AvantageType extends AbstractType
{
   public function buildForm(FormBuilderInterface $builder, array $options)
   {
      $builder
            ->add('name', TextType::class, [
               'attr' => [
                  'placeholder' => 'form.avantages.placeholder'
               ]
            ])
      ;
   }

   public function configureOptions(OptionsResolver $resolver)
   {
      $resolver->setDefaults([
          'data_class' => Avantage::class,
          'translation_domain' => 'project',
      ]);
   }
}
