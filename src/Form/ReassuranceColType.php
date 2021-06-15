<?php

namespace App\Form;

use App\Entity\ReassuranceCol;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ReassuranceColType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $cols = [
            '1/1' => 12,
            '3/4' => 9,
            '2/3' => 8,
            '1/2' => 6,
            '1/3' => 4,
            '1/4' => 3,
        ];
        $builder
            ->add('col', ChoiceType::class, [ 'choices' => $cols])
            ->add('imgFile', VichImageType::class, [
                'required' => false
            ])
            ->add('description', CKEditorType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ReassuranceCol::class,
        ]);
    }
}
