<?php

namespace App\Controller\Admin\Crud;

use App\Entity\ProductCategory;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductCategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ProductCategory::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInPlural("Catégories Produits")->setEntityLabelInSingular("Catégorie Produits")->setDefaultSort(["id" => "DESC"]);
    }

    public function configureFields(string $pageName): iterable
    {
        return [

           TextField::new("title", "nom"),
           TextareaField::new("description"),
           AssociationField::new("parent")
           ->setFormTypeOptions([])

        ];
    }
}
