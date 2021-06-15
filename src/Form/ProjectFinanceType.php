<?php

namespace App\Form;

use App\Entity\ProjectFinance;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectFinanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('detail', null, [
               'label' => false,
                'attr' => [
                    'placeholder' => 'form.finance.placeholder'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProjectFinance::class,
            'translation_domain' => 'project'
        ]);
    }
}
