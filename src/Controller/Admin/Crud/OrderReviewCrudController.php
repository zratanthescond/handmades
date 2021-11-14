<?php

namespace App\Controller\Admin\Crud;

use App\Entity\OrderReview;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class OrderReviewCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return OrderReview::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInPlural("Avis Commandes")->setEntityLabelInSingular("Avis Commandes")->setDefaultSort(["createdAt" => "DESC"]);
    }

    public function configureActions(Actions $actions): Actions
    {
         return $actions->disable(Action::NEW);
    }

   public function configureFields(string $pageName): iterable
   {
       return [
        
         DateTimeField::new("createdAt", "Date")->setFormTypeOptions(["disabled" => true]),
         AssociationField::new("cOrder", "Client")->onlyOnIndex()->formatValue(fn($e, $v) => $v->getCOrder()->getUser()),
         IntegerField::new("deliveryRating", "Note livraison"),
         TextareaField::new("suggestion", "suggestion"),
         BooleanField::new("isValidated", "Validé")
        // AssociationField::new("cOrder", "x")->formatValue()

       ];
   }
}
