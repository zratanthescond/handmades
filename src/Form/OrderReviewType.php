<?php

namespace App\Form;

use App\Entity\OrderReview;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $choices = array_combine(range(1, 5), range(1, 5));

        uksort($choices, fn($a, $b) => $b > $a );
        
        $builder
            ->add('deliveryRating', ChoiceType::class, [

                "choices" => $choices,
                "label" => "Noter la livraison sur 5",
                "required" => true
            ])
            ->add('suggestion', TextareaType::class, [

                "label" => "Avez-vous des suggestions ?",
                "required" => false
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OrderReview::class,
        ]);
    }
}
