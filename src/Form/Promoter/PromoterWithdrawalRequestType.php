<?php

namespace App\Form\Promoter;

use App\Entity\Promoter;
use App\Entity\PromoterWithdrawalRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

class PromoterWithdrawalRequestType extends AbstractType
{

    public const WITHDRAWALS_METHODS = [

        "Virement bancaire",
        "Par chéque",
        "Espéce"
    ];


    private Promoter $promoter;

    public function __construct(Security $security)
    {
        $this->promoter = $security->getUser();
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add("lastName", null, [

                "mapped" => false,
                "label" => "Nom",
                "data" => $this->promoter->getLastName(),
                "disabled" => true
            ])
            ->add("firstName", null, [

                "mapped" => false,
                "label" => "Prénom",
                "data" => $this->promoter->getFirstName(),
                "disabled" => true
            ])

            ->add('amount', MoneyType::class, [

                "label" => "Montant",
                "currency" => "TND",
                "help" => "Minimum 200 TND",
                "constraints" => [

                    new NotBlank(),
                    new LessThanOrEqual($this->promoter->getBalance())
                ]
            ])
            ->add('method', ChoiceType::class, [

                "choices" => array_combine(self::WITHDRAWALS_METHODS, self::WITHDRAWALS_METHODS),
                "label" => "Méthode de retrait"
            ])

            ->add("submit", SubmitType::class, [

                "label" => "Valider"
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PromoterWithdrawalRequest::class,
        ]);
    }
}
