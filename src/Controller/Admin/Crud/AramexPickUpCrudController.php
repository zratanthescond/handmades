<?php

namespace App\Controller\Admin\Crud;

use App\Entity\AramexPickUp;
use App\Form\AramexPickUpType;
use App\Service\Aramex\AramexPickUp as AramexAramexPickUp;
use App\Service\Aramex\AramexTracking;
use App\Service\Aramex\AramexHelper;
use App\Entity\AramexShipement;
use App\Entity\AramexTracking as EntityAramexTracking;
use App\Repository\AramexTrackingRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class AramexPickUpCrudController extends AbstractCrudController
{


    private $aramexApi;

    private $aramexTrackingApi;

    private $em;

    private AramexTrackingRepository $trackingRepo;

    private $adminUrlGenerator;

    public function __construct(
        AramexAramexPickUp $aramexApi,
        AramexTracking $aramexTrackingApi,
        EntityManagerInterface $em,
        AdminUrlGenerator $adminUrlGenerator
    ) {
        $this->aramexApi = $aramexApi;

        $this->aramexTrackingApi = $aramexTrackingApi;

        $this->trackingRepo = $em->getRepository(EntityAramexTracking::class);

        $this->em = $em;

        $this->adminUrlGenerator = $adminUrlGenerator;
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
        })->disable(Action::DELETE, Action::EDIT)->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function detail(AdminContext $context)
    {

        $pickUp = $context->getEntity()->getInstance();

        $shippements = $pickUp->getShippements();


        return $this->render("/dashboard/pickup/details.html.twig", [
            "pickUp" => $pickUp,
            "shippements" => $shippements
        ]);
    }


    public function new(AdminContext $context)
    {
        $aramexPickUp = new AramexPickUp();

        $form = $this->createForm(AramexPickUpType::class, $aramexPickUp);

        $request = $context->getRequest();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            try {

                $shippements = $aramexPickUp->getShippements();

                $trackingsIds = array_map(fn (AramexShipement $e) => $e->getTrackingId(),  $shippements->toArray());

                $pickUpRequest = $this->aramexApi->createPickUp($aramexPickUp->getReadyTime(), $aramexPickUp->getLastPickupTime());

                $aramexPickUp->setPickUpId($pickUpRequest->getId())
                    ->setGuid($pickUpRequest->getGuid());

                $this->em->persist($aramexPickUp);

                $updatedTrackings = [];

                try {

                    $trackingResults = $this->aramexTrackingApi->trackMultiple($trackingsIds);

                    $HasMultipleResults = AramexHelper::isMultiDimensional($trackingResults);

                    if ($HasMultipleResults) {

                        foreach ($trackingResults as $result) {

                            $trackingResult = (array) $result["Value"]["TrackingResult"];

                            $trackingEntity = $this->trackingRepo->findOneBy(["waybillNumber" => $trackingResult["WaybillNumber"]]);

                            array_push($updatedTrackings, $trackingEntity);

                            $trackingEntity->setData($trackingResult)->setUpdateCode($trackingResult["UpdateCode"]);

                            $this->em->persist($trackingEntity);
                        }
                    } else {

                        $trackingResult = (array) $trackingResults["Value"]["TrackingResult"];

                        $trackingEntity = $this->trackingRepo->findOneBy(["waybillNumber" => $trackingResult["WaybillNumber"]]);

                        $trackingEntity->setData($trackingResult)->setUpdateCode($trackingResult["UpdateCode"]);

                        array_push($updatedTrackings, $trackingEntity);

                        $this->em->persist($trackingEntity);
                    }

                    $this->addFlash("success", sprintf("Pick up %s crée avec succés", $pickUpRequest->getId()));

                    $this->em->flush();

                    $url = $this->adminUrlGenerator
                        ->setController(self::class)
                        ->setAction(Action::DETAIL)
                        ->setEntityId($aramexPickUp->getId())
                        ->generateUrl();

                    return $this->redirect($url);
                } catch (Exception $e) {

                    $this->addFlash("danger", $e->getMessage());
                }
            } catch (\Exception $e) {

                $this->addFlash("danger", $e->getMessage());
            }
        }

        return $this->render("dashboard/aramex/aramex_pick_up.html.twig", [

            "form" => $form->createView()
        ]);
    }
}
