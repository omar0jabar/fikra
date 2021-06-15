<?php

namespace App\Form;

use App\Entity\Company;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * Class ChangeLogoCompanyType
 * @package App\Form
 */
class ChangeLogoCompanyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
   public function buildForm(FormBuilderInterface $builder, array $options)
   {
      $builder
         ->add('logoFile', VichImageType::class, [
            'label' => 'form.logoFile.label',
            'attr' => [
               'placeholder' => 'form.logoFile.placeholder',
               'onchange' => 'showIMG(this)'
            ],
            'help' => 'form.logoFile.help',
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
