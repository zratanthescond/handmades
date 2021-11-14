<?php

namespace App\Controller\Admin\Crud;

use App\Entity\ProductType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductTypeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ProductType::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [

             TextField::new("name", "Nom")
        ];
    }

     public function configureCrud(Crud $crud): Crud
     {
         return $crud->setEntityLabelInPlural("Types de produit")->setEntityLabelInSingular("Type");
     }
}
