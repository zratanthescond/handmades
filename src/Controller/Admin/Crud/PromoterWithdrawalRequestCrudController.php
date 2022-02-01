<?php

namespace App\Controller\Admin\Crud;

use App\Entity\PromoterWithdrawalRequest;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PromoterWithdrawalRequestCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PromoterWithdrawalRequest::class;
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
