<?php

namespace App\Controller\Admin\Crud;

use App\Core\Security\Permission\UserRoles;
use App\Entity\PromoterWithdrawalRequest;
use App\Form\Promoter\PromoterWithdrawalRequestType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PromoterWithdrawalRequestCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PromoterWithdrawalRequest::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityPermission(UserRoles::SUPER_ADMIN)
            ->setEntityLabelInPlural("Demandes de retrait")
            ->setEntityLabelInSingular("Demande de retraits")
            ->setDefaultSort(["createdAt" => "DESC"]);
    }

    public function configureActions(Actions $actions): Actions
    {

        return $actions->disable(Action::DELETE, Action::NEW);
    }

    public function configureFields(string $pageName): iterable
    {

        return [

            DateField::new("createdAt", "Date")->setFormTypeOptions(["disabled" => true]),
            AssociationField::new("promoter", "Influenceur")->setFormTypeOptions(["disabled" => true]),
            MoneyField::new("amount", "Montant")
                ->setCurrency("TND")
                ->setStoredAsCents(false)
                ->setColumns(6)
                ->setFormTypeOptions(["disabled" => true])
                ->hideOnForm(),

            ChoiceField::new("method", "Méthode")->setChoices(
                array_combine(PromoterWithdrawalRequestType::WITHDRAWALS_METHODS, PromoterWithdrawalRequestType::WITHDRAWALS_METHODS)
            ),
            ChoiceField::new("status", "Statut")->setChoices([

                "On Hold" => "On Hold",
                "Validated" => "Validated",
            ])


        ];
    }
}
