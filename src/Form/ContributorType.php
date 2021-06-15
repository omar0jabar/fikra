<?php

namespace App\Form;

use App\Entity\Contributor;
use App\Helper\DataHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ContributorType
 * @package App\Form
 */
class ContributorType extends AbstractType
{
    /**
     * @var DataHelper
     */
    private $dataHelper;

    /**
     * ContributorType constructor.
     *
     * @param DataHelper $dataHelper
     */
    public function __construct(
        DataHelper $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', null, [
                'label' => 'form.firstName.label',
                'attr' => [
                    'placeholder' => 'form.firstName.placeholder'
                ]
            ])
            ->add('lastName', null, [
                'label' => 'form.lastName.label',
                'attr' => [
                    'placeholder' => 'form.lastName.placeholder'
                ]
            ])
            ->add('isAnonymous', null, [
                'label' => 'form.isAnonymous.label'
            ])
            ->add('email', null, [
                'label' => 'form.email.label',
                'attr' => [
                    'placeholder' => 'form.email.placeholder'
                ]
            ])
            ->add('contributionAmount', null, [
                'label' => 'form.contributionAmount.label',
                'attr' => [
                    'placeholder' => 'form.contributionAmount.placeholder'
                ],
            ])
            ->add('chosenPayment', ChoiceType::class, [
                'label' => 'form.chosenPayment.label',
                'choices' => $this->dataHelper->getPaymentChoice(),
                'expanded' => true
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contributor::class,
            'translation_domain' => 'company',
        ]);
    }
}
