<?php

namespace App\Controller\Admin\Crud;

use App\Entity\BlogCategory;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class BlogCategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BlogCategory::class;
    }

   
    public function configureFields(string $pageName): iterable
    {
        return [

             TextField::new("title", "Nom")
        ];
    }
}
