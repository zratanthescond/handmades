<?php

namespace App\Controller\Admin\Crud;

use App\Entity\User;
use App\Entity\UserAddress;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\RequestStack;

class UserAddressCrudController extends AbstractCrudController
{

    private $request;

    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
    }

    public function createEntity(string $entityFqcn)
    {

        $address = new UserAddress();

        $userId = $this->request->query->get("userId");

        if ($userId) {

            $user = $this->getdoctrine()->getRepository(User::class)->find($userId);

            if ($user) {

                $address->setUser($user);
            }
        }

        return $address;
    }

    public static function getEntityFqcn(): string
    {
        return UserAddress::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInPlural("Adresses")
            ->setEntityLabelInSingular("Adresse");
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable(Action::DELETE)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new("user", "Client")->autocomplete()->setFormTypeOption("disabled", true)->setColumns(12),
            TextareaField::new("address", "Adresse")->setColumns(12),
            TextField::new("town", "Ville")->setColumns(6),
            IntegerField::new("postalCode", "Code Postal")->setColumns(6),
            BooleanField::new("isDefault", "Adresse par défault")->setColumns(12)

        ];
    }
}
