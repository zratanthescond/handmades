<?php

namespace App\Controller\Admin\Crud;

use App\Entity\Blog;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class BlogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Blog::class;
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig')
                     ->setDefaultSort(["createdAt" => "DESC"]);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
           
            TextField::new("title"),
            AssociationField::new("category")->setRequired(true),
            TextEditorField::new("content")->onlyOnForms()->setFormType(CKEditorType::class),
            TextField::new("imageFile")->setFormType(VichImageType::class)->onlyOnForms()
           

        ];
    }
}
