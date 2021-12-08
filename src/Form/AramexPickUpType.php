<?php

namespace App\Form;

use App\Entity\AramexPickUp;
use App\Entity\AramexShipement;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AramexPickUpType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('shippements', null, [

                "class" => AramexShipement::class,

                "query_builder" => function (EntityRepository $er) {

                    return $er->createQueryBuilder("s")->andWhere("s.aramexPickUp is NULL");
                },

                "attr" => [

                    "data-ea-widget" => "ea-autocomplete"
                ],

                "by_reference" => false,
            ])

            ->add("readyTime", DateTimeType::class, [

                "widget" => "single_text",

                "help" => "de 09 au 19h"
            ])

            ->add("lastPickupTime", DateTimeType::class, [

                "widget" => "single_text",

                "help" => "de 09 au 19h"
            ])

            ->add("submit", SubmitType::class, [

                "label" => "Envoyer",

            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AramexPickUp::class,
        ]);
    }
}
