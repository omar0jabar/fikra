<?php

namespace App\Form;

use App\Entity\Event;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $obj = $builder->getData();
       $required = $obj === null ? true : false;
        $builder
           ->add('date', DateType::class, [
              'widget' => 'single_text'
           ])
           ->add('isActive')
           ->add('uploadBannerDesktop', VichImageType::class, [
              'required' => $required
           ])
           ->add('uploadBannerMobile', VichImageType::class, [
              'required' => $required
           ])
           ->add('title')
           ->add('content', CKEditorType::class)
           ->add('metaTitle')
           ->add('metaTags')
           ->add('metaDescription')
           ->add('slug', null, [
              'required' => false
           ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
