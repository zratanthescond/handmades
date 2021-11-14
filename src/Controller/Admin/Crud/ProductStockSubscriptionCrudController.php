<?php

namespace App\Controller\Admin\Crud;

use App\Entity\ProductStockSubscription;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductStockSubscriptionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ProductStockSubscription::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInSingular("Notifications")->setEntityLabelInPlural("Notications")->setDefaultSort(["createdAt" => "DESC"]);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable(Action::NEW);
    }

    public function configureFields(string $pageName): iterable
    {
        return [

            AssociationField::new("product", "Produit")->onlyOnIndex(),
            TextField::new("email"),
            TextField::new("firstName", "nom"),
            TextField::new("lastName")
        ];
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
