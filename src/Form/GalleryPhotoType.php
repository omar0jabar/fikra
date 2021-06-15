<?php

namespace App\Form;

use App\Entity\GalleryPhoto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class GalleryPhotoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('imageFile', VichImageType::class, [
                'translation_domain' => 'galleryPhotos',
                'attr' => [
                    "onchange" => "showIMG(this)",
                    'placeholder' => "form.imageFile.placeholder",
                    'required' => false
                ],
                'download_uri' => false,
                "allow_delete" => false,
            ])
            ->add('alt', null, [
                'attr' => [
                    'placeholder' => 'form.alt.placeholder',
                    'required' => false
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => GalleryPhoto::class,
            'translation_domain' => 'galleryPhotos',
        ]);
    }
}