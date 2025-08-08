<?php

namespace App\Form;

use App\Entity\OrderSpecialDiscount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderSpecialDiscountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('value', MoneyType::class, [

                "currency" => "TND",
                "label" => "Valeur"
            ])

            ->add("description")

            ->add("privateDescription", TextareaType::class, [

                "required" => true,
                "label" => "Description privée",
                "help" => "Affiché seulement par l'admin"
            ])

            ->add("submit", SubmitType::class, [

                "label" => "Valider"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OrderSpecialDiscount::class,
            //"max" => null
        ]);
    }
}
