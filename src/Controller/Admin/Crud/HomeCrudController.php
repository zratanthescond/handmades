<?php

namespace App\Controller\Admin\Crud;

use App\Entity\Home;
use App\Form\HomeSliderType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;

class HomeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Home::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable(Action::NEW, Action::DELETE);
    }

    public function configureFields(string $pageName): iterable
    {
        return [

            FormField::addPanel("Sliders"),
            CollectionField::new("sliders", "Images")->setEntryType(HomeSliderType::class),
            FormField::addPanel("Produits en vedettes"),
            AssociationField::new("featuredProducts", "Selectionner des produits")
            ->autocomplete()->setFormTypeOptions(["by_reference" => false])
        ];
    }
}
