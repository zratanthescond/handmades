<?php

namespace App\Controller\Admin\Crud;

use App\Entity\Page;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class PageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Page::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig')
                     ->setDefaultSort(["createdAt" => "DESC"]);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            
            DateField::new("createdAt")->onlyOnIndex(),
            IntegerField::new("id")->onlyOnIndex(),
            TextField::new("title"),
            TextEditorField::new("content")->setFormType(CKEditorType::class)->onlyOnForms()

        ];
    }

   
}
