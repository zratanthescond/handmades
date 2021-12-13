<?php

namespace App\Controller\Admin\Crud;

use App\Entity\Parrainage;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ParrainageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Parrainage::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInPlural("Parrainages")
            ->setEntityLabelInSingular("Parrainage")
            ->setDefaultSort(["createdAt" => "DESC"]);
    }


    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable(Action::DELETE, Action::NEW);
    }

    public function configureFields(string $pageName): iterable
    {
        return [

            DateTimeField::new("createdAt", "Date")->setFormTypeOptions(["disabled" => true]),
            AssociationField::new("fromUser", "Client")->setFormTypeOptions(["disabled" => true]),
            TextField::new("beneficiaryFullName", "Bénéficiaire")->onlyOnIndex(),
            EmailField::new("beneficiaryEmail", "Email")->setFormTypeOptions(["disabled" => true]),
            BooleanField::new("isRewarded", "Récompensé")->renderAsSwitch(false)->setFormTypeOptions(["disabled" => true])


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
