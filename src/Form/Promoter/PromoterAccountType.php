<?php

namespace App\Form\Promoter;

use App\Entity\Promoter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PromoterAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', null, [

                "label" => "Prénom",

                "disabled" => true

            ])
            ->add('lastName', null, [

                "label" => "Nom",
                "disabled" => true
            ])
            ->add('email', EmailType::class, [

                "disabled" => true,

                "help" => "Contacter nous si vous souhaitez changer votre adresse email"
            ])
            ->add('alias', null, [

                "help" => "Exemple : Votre pseudo Instagram",

            ])
            ->add('phoneNumber', NumberType::class, [

                "label" => "Numéro de téléphone"
            ])
            
            ->add("submit", SubmitType::class, [

                "label" => "Mettre à jour"
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Promoter::class,
        ]);
    }
}
