<?php

namespace EgioDigital\CMSBundle\Form;

use EgioDigital\CMSBundle\Entity\Page;
use EgioDigital\CMSBundle\Repository\CategoryPageRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $obj = $builder->getData();
        $required = $obj->getId() === null ? true : false;
        $builder
            ->add('lang', ChoiceType::class, [
                'label' => 'Langue',
                'choices' => [
                    'Anglais' => 'en',
                    'Français' => 'fr'
                ]
            ])
            ->add('category', null, [
                'placeholder' => '-- Selectionner --',
                'required' => false
            ])
            ->add('title')
            ->add('slug', HiddenType::class, ['required' => false])
            ->add('isActive')
            ->add('isPageSimple')
            ->add('metaTitle')
            ->add('metaTags')
            ->add('metaDescription')
            ->add('uploadBannerDesktop', VichImageType::class, ['required' => $required])
            ->add('uploadBannerMobile', VichImageType::class, ['required' => $required])
            ->add('contentBanner', CKEditorType::class)
            ->add('htmlIdAttr', null, [
                'required' => false
            ])
            ->add('htmlClassAttr', null, [
                'required' => false
            ])
            ->add('blocks', CollectionType::class, [
                'entry_type' => BlockType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'label' => false,
                'by_reference' => false,
            ]);

        if ($obj->getId() !== null) {
            $builder->add('preview', SubmitType::class, [
                'label' => 'Preview',
                'attr' => [
                    'class' => 'btn btn-info'
                ]
            ]);
        }

        $builder->add('saveDraft', SubmitType::class, [
                'label' => 'Save Draft',
                'attr' => [
                    'class' => 'btn btn-warning'
                ]
            ])
            ->add('savePublic', SubmitType::class, [
                'label' => 'Save public',
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Page::class,
        ]);
    }
}