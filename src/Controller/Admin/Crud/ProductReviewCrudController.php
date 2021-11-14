<?php

namespace App\Controller\Admin\Crud;

use App\Entity\ProductReview;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class ProductReviewCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ProductReview::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInPlural("Avis Produits")->setEntityLabelInSingular("Avis Produits")->setDefaultSort(["createdAt" => "DESC"]);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable(Action::NEW);
    }

    public function configureFields(string $pageName): iterable
    {
        return [

            DateTimeField::new("createdAt", "Date")->setFormTypeOptions(["disabled" => true]),
            AssociationField::new("uOrder", "Client")->onlyOnIndex()->formatValue(fn($e, $v) => $v->getUOrder()->getUser()),
            AssociationField::new("product")->autocomplete()->setFormTypeOptions(["disabled" => true]),
            IntegerField::new("note"),
            TextareaField::new("comment", "Commentaire"),
            BooleanField::new("isValidated", "Validé")
        ];
    }
}
