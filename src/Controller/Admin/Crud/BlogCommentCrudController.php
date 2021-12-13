<?php

namespace App\Controller\Admin\Crud;

use App\Entity\BlogComment;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class BlogCommentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BlogComment::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInSingular("Commentaire")
        ->setEntityLabelInPlural("Commentaires")
        ->setDefaultSort(["createdAt" => "DESC"]);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
          
            DateTimeField::new("createdAt", "Date")->setFormTypeOptions(["disabled" => true])->setColumns(12),
            AssociationField::new("blog", "Article")->autocomplete()->setFormTypeOptions(["disabled" => true])->setColumns(6),
            AssociationField::new("user", "Client")->autocomplete()->setFormTypeOptions(["disabled" => true])->setColumns(6),
            BooleanField::new("isValidated", "Validé")->setColumns(12),
            TextareaField::new("comment", "Commentaire")->setColumns(12)

        ];
    }
}
