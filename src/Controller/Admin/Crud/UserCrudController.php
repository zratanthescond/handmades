<?php

namespace App\Controller\Admin\Crud;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInPlural("Clients")->setEntityLabelInSingular("Client");
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable(Action::DELETE, Action::NEW);
    }

    public function configureFields(string $pageName): iterable
    {
        return [

             IntegerField::new("id")->setFormTypeOptions(["disabled" => true]),
             EmailField::new("email"),
             DateField::new("createdAt", "Date d'inscription")->setFormTypeOptions(["disabled" => true]),
             TextField::new("fullName", "Nom et prénom")->onlyOnIndex(),
             TextField::new("firstName", "Nom")->onlyOnForms(),
             TextField::new("lastName", "Prénom")->onlyOnForms(),
             TextField::new("phoneNumber", "Numéro de téléphone"),
             DateField::new("birthDay", "Date de naissance"),
            TextField::new("defaultAddress")->setFormTypeOptions(["disabled" => true])


        ];
    }
}
