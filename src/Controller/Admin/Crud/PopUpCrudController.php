<?php

namespace App\Controller\Admin\Crud;

use App\Entity\PopUp;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;

class PopUpCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PopUp::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable(Action::NEW, Action::DELETE);
    }

    public function configureFields(string $pageName): iterable
    {

        return [

            TextField::new("link", "LIen"),
            BooleanField::new("isActive", "Active"),
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
