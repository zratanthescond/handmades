<?php

namespace App\Controller\Admin\Crud;

use App\Core\Security\Permission\UserRoles;
use App\Entity\DiscountCode;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\PercentField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class DiscountCodeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DiscountCode::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInPlural("Codes promo")
        ->setEntityLabelInSingular("Code promo")
        ->setDefaultSort(["createdAt" => "DESC"])
        ->setEntityPermission(UserRoles::SUPER_ADMIN);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
           
            IntegerField::new("id")->onlyOnIndex(),
            TextField::new("code")->setColumns(6)->setHelp("4 caractères minimum"),
            PercentField::new("percentage", "Valeur")->setColumns(6)->setStoredAsFractional(false)->setHelp("Valeur en pourcentage"),
            AssociationField::new("promoter")->setColumns(6)->setHelp("Associer le code promo à un promoteur"),
            DateTimeField::new("expirationDate")->setColumns(6)->setHelp("Date d'expiration du code"),
            BooleanField::new("isValid", "Valid")->setColumns(6)->setHelp("Si n'est pas valid, le code n'est pas accepté")


        ];
    }
}
