<?php

namespace App\Controller\Admin\Crud;

use App\Entity\AramexPickUp;
use App\Form\AramexPickUpType;
use App\Service\Aramex\AramexPickUp as AramexAramexPickUp;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class AramexPickUpCrudController extends AbstractCrudController
{

    private $aramexApi;

    public function __construct(AramexAramexPickUp $aramexApi)
    {
        $this->aramexApi = $aramexApi;
    }


    public static function getEntityFqcn(): string
    {
        return AramexPickUp::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInPlural("Pick Up")
            ->setEntityLabelInSingular("Pick Up")
            ->setDefaultSort(["createdAt" => "DESC"]);
    }


    public function configureActions(Actions $actions): Actions
    {
        return $actions->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
            return $action->setLabel("Créer Pick up request");
        })->disable(Action::DELETE, Action::EDIT);
    }

    public function new(AdminContext $context)
    {
        $aramexPickUp = new AramexPickUp();

        $form = $this->createForm(AramexPickUpType::class, $aramexPickUp);

        $request = $context->getRequest();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $pickUpRequest = $this->aramexApi->createPickUp($aramexPickUp->getReadyTime(), $aramexPickUp->getLastPickupTime());

            $aramexPickUp->setPickUpId($pickUpRequest->getId())
                ->setGuid($pickUpRequest->getGuid());

            $em = $this->getDoctrine()->getManager();

            $em->persist($aramexPickUp);

            $em->flush();
        }

        return $this->render("dashboard/aramex/aramex_pick_up.html.twig", [

            "form" => $form->createView()
        ]);
    }

    
}
