<?php

namespace App\Controller\Admin\Crud;

use App\Entity\SiteInfo;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SiteInfoCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SiteInfo::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [

           TextField::new("facebook")->setColumns(4),
           
           TextField::new("instagram")->setColumns(4),

           TextField::new("YoutubeVideo")->setHelp("Le key de la video")->setColumns(4),

           EmailField::new("email")->setColumns(6),

           TextField::new("MobilePhone")->setColumns(6),


           TextareaField::new("fullAdress", "Adresse")->setColumns(12)
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
