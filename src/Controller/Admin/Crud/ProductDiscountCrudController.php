<?php

namespace App\Controller\Admin\Crud;

use App\Core\Security\Permission\UserRoles;
use App\Entity\ProductDiscount;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\PercentField;

class ProductDiscountCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ProductDiscount::class;
    }

    public function configureActions(Actions $actions): Actions
    {
      
        return $actions->disable(Action::DELETE);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInPlural("Promotions produits")
        ->setEntityLabelInSingular("Promotion produits")
        ->setDefaultSort(["createdAt" => "DESC"])
        ->setEntityPermission(UserRoles::SUPER_ADMIN);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
        
            PercentField::new("pourcentage")->setStoredAsFractional(false),
            DateField::new("expireAt"),
            AssociationField::new("product")

        ];
    }
}
