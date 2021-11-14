<?php

namespace App\Controller\Admin\Crud;

use App\Entity\Brand;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CountryField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class BrandCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Brand::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInPlural("Marques")->setEntityLabelInSingular("Marque")->setDefaultSort(["id" => "DESC"]);
    }

    public function configureFields(string $pageName): iterable
    {
        return [

            TextField::new("name"),
            CountryField::new("country", "Pays d'origine"),
            TextField::new("imageFile")->setFormType(VichImageType::class)->onlyOnForms()
        ];
    }

}
