<?php

namespace App\Form;

use App\Entity\Images;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ImageCategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Image', FileType::class, [
                'label' => 'Photo de la catégorie',
                'mapped'=> false,

                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/jpg',
                            'image/png',
                        ]
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Images::class,
        ]);
    }
}
