<?php

namespace App\Controller\Admin\Crud;

use App\Entity\AramexShipement;
use App\Service\Aramex\AramexTrackingUpdateCodeResolver;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AramexShipementCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AramexShipement::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInPlural("Shipements")
            ->setEntityLabelInSingular("Shipements")
            ->setDefaultSort(["createdAt" => "DESC"]);
    }


    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable(Action::NEW, Action::DELETE, Action::EDIT)->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function detail(AdminContext $context)
    {

        $shippement = $context->getEntity()->getInstance();

        $tracking = $shippement->getTracking();

        return $this->render("dashboard/shippement/details.html.twig", [

            "shippement" => $shippement,
            "tracking" => $tracking
        ]);
    }


    public function configureFields(string $pageName): iterable
    {
        return [

            DateTimeField::new("createdAt", "Date de création"),
            AssociationField::new("clientOrder", "Commande"),
            TextField::new("trackingId"),
            TextField::new("attachement", "Bordereau")->setTemplatePath("dashboard/fields/aramex_shippement.html.twig"),
            AssociationField::new("clientOrder", "Client")
                ->formatValue(fn ($v, AramexShipement $e) => $e->getClientOrder()->getUser()->getFullName()),
            BooleanField::new("isPickedUp", "Picked UP")->renderAsSwitch(false),
            TextField::new("status", "Statut")
                ->formatvalue(fn ($k, AramexShipement $e) => AramexTrackingUpdateCodeResolver::getStatus($e->getTracking()->getUpdateCode()))
        ];
    }
}
