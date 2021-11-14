<?php

namespace App\Controller\Admin\Crud;

use App\Core\Security\Permission\UserRoles;
use App\Entity\HelpSection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class HelpSectionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return HelpSection::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityPermission(UserRoles::SUPER_ADMIN);
    }

   public function configureFields(string $pageName): iterable
   {
       return [

           TextField::new("title"),
           TextareaField::new("content")
       ];
   }
}
