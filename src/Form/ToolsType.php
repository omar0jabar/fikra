<?php

namespace App\Form;

use App\Entity\Tools;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ToolsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $obj = $builder->getData();

        $builder
            ->add('title')
            ->add('content')
            ->add('uploadFile', VichFileType::class, [
                'required' => $obj->getId() === null ? true : false
            ])
            ->add('icon', ChoiceType::class, [
                'choices' => [
                    [
                        'icon 0' => '0',
                        'icon 1' => '1',
                        'icon 2' => '2',
                    ]
                ],
                'expanded'=>true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tools::class,
        ]);
    }
}
