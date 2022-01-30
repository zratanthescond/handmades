<?php

namespace App\Controller\Admin\Crud;

use App\Entity\UserAddress;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserAddressCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return UserAddress::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInPlural("Adresses")->setEntityLabelInSingular("Adresse");
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable(Action::DELETE, Action::NEW)->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextareaField::new("address", "Adresse")->setColumns(12),
            TextField::new("town", "Ville")->setColumns(6),
            IntegerField::new("postalCode", "Code Postal")->setColumns(6),
            BooleanField::new("isDefault", "Adresse par défault")->setFormTypeOptions(["disabled" => true])->setColumns(12)
            
           
        ];
    }
}
