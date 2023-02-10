<?php

namespace App\Form;

use App\Entity\CategorieDeServices;
use App\Entity\CodePostal;
use App\Entity\Commune;
use App\Entity\Localite;
use App\Entity\Prestataire;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prestataire', TextType::class, [
                'required' => false
            ])
            ->add('localite', EntityType::class, [
                'class' => Localite::class,
                'placeholder'=> 'Localité',
                'required' => false
            ])
            ->add('categorie', EntityType::class, [
                'class' => CategorieDeServices::class,
                    'placeholder'=> 'Catégorie',
                    'required'=> false
                ])
            ->add('cp', EntityType::class, [
                'class' => CodePostal::class,
                'placeholder' => 'Code Postal',
                'required' => false
            ])
            ->add('commune', EntityType::class, [
                'class' => Commune::class,
                'placeholder' => 'Commune',
                'required'=> false
            ])
        ;

    }

    /*public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Prestataire::class,
        ]);
    }*/
}
