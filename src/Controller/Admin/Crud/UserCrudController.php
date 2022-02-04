<?php

namespace App\Controller\Admin\Crud;

use App\Core\Security\Permission\UserRoles;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInPlural("Clients")
        ->setEntityLabelInSingular("Client")
        ->setDefaultSort(["createdAt" => "DESC"]);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable(Action::DELETE, Action::NEW)->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function detail(AdminContext $context)
    {

        $client = $context->getEntity()->getInstance();


        return $this->render("dashboard/client/details.html.twig", [
            "client" => $client
        ]);
    }

    public function configureFields(string $pageName): iterable
    {
        $user = $this->getUser();

        $disabled = in_array(UserRoles::SUPER_ADMIN, $user->getRoles()) ? false : true;

        return [

            IntegerField::new("id")->setFormTypeOptions(["disabled" => true])->setColumns(6),
            DateField::new("createdAt", "Date d'inscription")->setFormTypeOptions(["disabled" => true])->setColumns(6),
            EmailField::new("email")->setFormTypeOptions(["disabled" => true])->setColumns(4),
            TextField::new("fullName", "Nom et prénom")->onlyOnIndex(),
            TextField::new("firstName", "Nom")->onlyOnForms()->setColumns(4),
            TextField::new("lastName", "Prénom")->onlyOnForms()->setColumns(4),
            TextField::new("phoneNumber", "Numéro de téléphone")->setColumns(6),
            DateField::new("birthDay", "Date de naissance")->setColumns(6),
            IntegerField::new("rewardPoints", "Points de fidélité")
                ->setFormTypeOptions(["disabled" => $disabled])
                ->setColumns(6)

        ];
    }
}
