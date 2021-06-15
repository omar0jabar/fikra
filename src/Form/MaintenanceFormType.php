<?php

namespace App\Form;

use App\Entity\Maintenance;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MaintenanceFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('isLocked')
            ->add('title')
            ->add('message')
            ->add('paragraph')
            ->add('ttl', DateTimeType::class, [
              'widget' => 'single_text'
            ])
          ->add('ips', CollectionType::class, [
            "entry_type" => IpType::class,
            "label" => false,
            "allow_add" => true,
            "allow_delete" => true,
            "required" => false,
          ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Maintenance::class,
        ]);
    }
}
