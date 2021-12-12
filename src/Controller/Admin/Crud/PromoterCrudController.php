<?php

namespace App\Controller\Admin\Crud;

use App\Core\Security\Permission\UserRoles;
use App\Entity\Promoter;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PromoterCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Promoter::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityPermission(UserRoles::SUPER_ADMIN);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable(Action::BATCH_DELETE)
        ->update(Crud::PAGE_INDEX, Action::DELETE, function ($dto) {

            return $dto->displayIf(static function (Promoter $entity) {

                return $entity->getDiscountCodes()->isEmpty();
            });
        });
    }

   public function configureFields(string $pageName): iterable
   {
       return [
           
           TextField::new("fullName", "Nom et prénom")->onlyOnIndex(),
           TextField::new("firstName", "Nom")->onlyOnForms(),
           TextField::new("lastName", "Prénom")->onlyOnForms(),
           TextField::new("alias")->setHelp("Par exemple : Pseudo Instagram. Ceci s'affiche chez le client"),
           EmailField::new("email"),
           BooleanField::new("isActif", "Actif")
       ];
   }
}
