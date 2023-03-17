<?php

namespace App\Form;

use App\Entity\CategorieDeServices;
use App\Entity\Promotion;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PromotionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class)
            ->add('description',TextareaType::class)
            ->add('documentPdf', FileType::class, [
                'label' => 'Document PDF',
                'mapped'=> false,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ]
                    ])
                ]
            ])
            ->add('debut', DateType::class)
            ->add('fin', DateType::class)
            ->add('afficheDe', DateType::class)
            ->add('affichageJusque', DateType::class)
            ->add('categorieDeServices',EntityType::class, [
                'class' => CategorieDeServices::class,
                'choice_label' => 'nom',
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('enregistrer', SubmitType::class);

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Promotion::class,
        ]);
    }
}
