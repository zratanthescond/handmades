<?php

namespace App\Controller\Admin\Crud;

use App\Entity\ProductCategory;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductCategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ProductCategory::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::BATCH_DELETE)
            ->update(Crud::PAGE_INDEX, Action::DELETE, function ($dto) {

                return $dto->displayIf(static function (ProductCategory $entity) {

                    return $entity->getProducts()->isEmpty();
                });
            });
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInPlural("Catégories Produits")
            ->setEntityLabelInSingular("Catégorie Produits")
            ->setDefaultSort(["id" => "DESC"]);
    }

    public function configureFields(string $pageName): iterable
    {
        return [

            TextField::new("title", "nom"),
            TextareaField::new("description"),
            CollectionField::new("products", "Produits")
                ->formatValue(fn ($v, ProductCategory $pc) => $pc->getProducts()->count())->onlyOnIndex(),
            AssociationField::new("parent")
                ->setFormTypeOptions([])

        ];
    }
}
