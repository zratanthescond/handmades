<?php

namespace App\Controller\Admin\Crud;

use App\Entity\SiteInfo;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
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

    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable(Action::NEW, Action::DELETE);
    }

    public function configureFields(string $pageName): iterable
    {
        return [

           TextField::new("facebook")->setColumns(6),
           
           TextField::new("instagram")->setColumns(6),

           TextField::new("youtubeVideo")->setHelp("Le key de la video")->setColumns(6),

           TextField::new("mobileYoutubeVideo")->setHelp("Le key de la video")->setColumns(6),

           EmailField::new("email")->setColumns(6),

           TextField::new("mobilePhone")->setColumns(6),

           TextareaField::new("fullAdress", "Adresse")->setColumns(12)
        ];
    }
}
