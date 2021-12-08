<?php

namespace App\Controller\Admin\Crud;

use App\Core\Security\Permission\UserRoles;
use App\Entity\Order;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;

class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureActions(Actions $actions): Actions
    {

        $orderDetails = Action::new('orderDetails', 'Détails', 'fa fa-file-invoice')
            ->linkToCrudAction('orderDetails');

        return $actions->add(Crud::PAGE_INDEX, $orderDetails)->disable(Action::NEW);
    }

    public function orderDetails(AdminContext $context)
    {

        $order = $context->getEntity()->getInstance();

        return $this->render("dashboard/order/order_details.html.twig", [

            "order" => $order
        ]);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInPlural("Commandes")
        ->setEntityLabelInSingular("Commande")
        ->setDefaultSort(["createdAt" => "DESC"])
        ->setEntityPermission(UserRoles::SUPER_ADMIN);
    }

    public function configureFields(string $pageName): iterable
    {
        return [

            IntegerField::new("id")->onlyOnIndex(),
            DateTimeField::new("createdAt", "Date"),
            AssociationField::new("user", "Client")->onlyOnIndex(),
            CollectionField::new("products", "Produits")->onlyOnIndex(),
            MoneyField::new("total")->setCurrency("TND")->setNumDecimals(3)->setStoredAsCents(false)

        ];
    }
}
