<?php

namespace App\Controller\Admin\Crud;

use App\Entity\PopUp;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class PopUpCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PopUp::class;
    }

    public function configureFields(string $pageName): iterable
    {

        return [

           TextField::new("link", "LIen"),
           TextField::new("imageFile", "Image")->setFormType(VichImageType::class)
           ->setFormTypeOptions(["allow_delete" => false])
           ->OnlyOnForms()
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
