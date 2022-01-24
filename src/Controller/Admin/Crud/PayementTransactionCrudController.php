<?php

namespace App\Controller\Admin\Crud;

use App\Entity\PayementTransaction;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PayementTransactionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PayementTransaction::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
       
        return $crud->setEntityLabelInPlural("Paiments")
        ->setEntityLabelInSingular("Paiement")
        ->setDefaultSort(["createdAt" => "DESC"]);
    }

    public function configureActions(Actions $actions): Actions
    {
       
        return $actions->disable(Action::NEW, Action::DELETE, Action::EDIT);
    }

 
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateTimeField::new('createdAt', 'Date'),
            TextField::new("ref"),
            DateTimeField::new('updatedAt', 'Mise à jour'),
            AssociationField::new('cOrder')
        ];
    }
    
}
