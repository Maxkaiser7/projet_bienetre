<?php

namespace App\Form;

use App\Entity\CodePostal;
use App\Entity\Commune;
use App\Entity\Localite;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class
UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('email')
            ->add('adresse_rue')
            ->add('adresse_n')
            ->add('commune', EntityType::class, [
                'class' => Commune::class,
                'choice_label' => 'commune'
            ])
            ->add('code_postal', EntityType::class, [
                'class' => CodePostal::class,
                'choice_label' => 'code_postal'
            ])
            ->add('localite', EntityType::class, [
                'class' => Localite::class,
                'choice_label' => 'localite'
            ])
        ->add('enregistrer', SubmitType::class);

    }

public
function configureOptions(OptionsResolver $resolver): void
{
    $resolver->setDefaults([
        'data_class' => Utilisateur::class,
    ]);
}
}
