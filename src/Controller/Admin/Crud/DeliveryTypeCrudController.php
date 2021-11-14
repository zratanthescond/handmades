<?php

namespace App\Controller\Admin\Crud;

use App\Core\Security\Permission\UserRoles;
use App\Entity\DeliveryType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class DeliveryTypeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DeliveryType::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInPlural("Types de livraison")
        ->setEntityLabelInSingular("Type de livraison")
        ->setEntityPermission(UserRoles::SUPER_ADMIN);
    }

    public function configureFields(string $pageName): iterable
    {
        return [

             TextField::new("name", "Nom"),
             TextField::new("time", "Durée de livraison")->setHelp("Exemple: 2 jours"),
             TextareaField::new("description"),
             MoneyField::new("price")->setCurrency("TND")->setStoredAsCents(false)->setHelp("Taper 0 si la livraison est gratuite"),
             BooleanField::new("isActif", "Actif")
        ];
    }
}
