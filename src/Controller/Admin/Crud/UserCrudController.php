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

            IntegerField::new("id")->setFormTypeOptions(["disabled" => true])->setColumns(6),
            DateField::new("createdAt", "Date d'inscription")->setFormTypeOptions(["disabled" => true])->setColumns(6), 
             EmailField::new("email")->setFormTypeOptions(["disabled" => true])->setColumns(4),
             TextField::new("fullName", "Nom et prénom")->onlyOnIndex(),
             TextField::new("firstName", "Nom")->onlyOnForms()->setColumns(4),
             TextField::new("lastName", "Prénom")->onlyOnForms()->setColumns(4),
             TextField::new("phoneNumber", "Numéro de téléphone")->setColumns(6),
             DateField::new("birthDay", "Date de naissance")->setColumns(6),
             IntegerField::new("rewardPoints", "Points de fidélité")->setFormTypeOptions(["disabled" => true])->setColumns(12)

        ];
    }
}
