<?php

namespace App\Form;

use App\Entity\Company;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * Class ChangeCoverCompanyType
 * @package App\Form
 */
class ChangeCoverCompanyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
   public function buildForm(FormBuilderInterface $builder, array $options)
   {
      $builder
         ->add('coverFile', VichImageType::class, [
            'label' => 'form.coverFile.label',
            'attr' => [
               'placeholder' => 'form.coverFile.placeholder',
               'onchange' => 'showIMG(this)'
            ],
            'help' => 'form.coverFile.help',
            'allow_delete' => false,
            'download_link' => false,
            'image_uri' => false,
            'translation_domain' => 'company',
         ])
      ;
   }

    /**
     * @param OptionsResolver $resolver
     */
   public function configureOptions(OptionsResolver $resolver)
   {
      $resolver->setDefaults([
         'data_class' => Company::class,
         'translation_domain' => 'company'
      ]);
   }
}
