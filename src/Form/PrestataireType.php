<?php

namespace App\Form;

use App\Entity\CategorieDeServices;
use App\Entity\Commune;
use App\Entity\Prestataire;
use App\Entity\Proposer;
use App\Entity\Stage;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrestataireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class)
            ->add('siteinternet', UrlType::class)
            ->add('numtel', TextType::class)
            ->add('numtva', TextType::class)
            ->add('categorieDeServices',EntityType::class, [
                'class' => CategorieDeServices::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => true,
            ])


        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Prestataire::class,
        ]);
    }
}
