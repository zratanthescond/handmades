<?php

namespace App\Controller\Admin\Crud;

use App\Core\Security\Permission\UserRoles;
use App\Entity\Help;
use App\Form\HelpSectionType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use FOS\CKEditorBundle\Form\Type\CKEditorType;


class HelpCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Help::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig')
        ->setDefaultSort(["title" => "DESC"])
        ->setEntityPermission(UserRoles::SUPER_ADMIN);
    }

    public function configureFields(string $pageName): iterable
    {
        return [

             TextField::new("title"),
             TextEditorField::new("description")->setFormType(CKEditorType::class)->onlyOnForms(),
             CollectionField::new("sections")->setEntryType(HelpSectionType::class)
        ];
    }
}
