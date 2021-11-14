<?php

namespace App\Form;

use App\Entity\ProductReview;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $choices = array_combine(range(1, 5), range(1, 5));

        uksort($choices, fn ($a, $b) => $b > $a);


        $builder
            ->add('note', ChoiceType::class, [

                "choices" => $choices,
                "label" => "Note",
                "help" => "Noter le produit sur 5"
            ])
            ->add('comment', TextareaType::class, [

                "label" => "Votre commentaire",
                "required" => false,
                "attr" => [

                    "rows" => 3
                ]
            ])
            //->add('product')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductReview::class,
        ]);
    }
}
