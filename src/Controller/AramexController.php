<?php

namespace App\Controller;

use App\Entity\AramexPickUp;
use App\Entity\AramexShipement;
use App\Entity\AramexTracking as EntityAramexTracking;
use App\Entity\UserAddress;
use App\Repository\AramexPickUpRepository;
use App\Repository\AramexTrackingRepository;
use App\Repository\OrderRepository;
use App\Service\Aramex\Aramex;
use App\Service\Aramex\AramexTracking;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Aramex\AramexHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Dompdf\Dompdf;
use Dompdf\Options as PdfOptions;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Twig\Environment as Twig;

class AramexController extends AbstractController
{

  /**
   * @Route("/aramex/create/shippement", name="create_shippement_aramex", methods={"POST"})
   */

  public function createShippement(
    Request $request,
    OrderRepository $orderRepo,
    Aramex $aramex,
    AramexTracking $aramexTrackingApi,
    HttpClientInterface $httpClient,
    EntityManagerInterface $em
  ): Response {

    $data = $request->toArray();

    if (!isset($data["orderId"]) || !isset($data["token"])) {

      return $this->json(["error" => "COULD NOT FIND TOKEN OR ORDERID"], 400);
    }

    $orderId = $data["orderId"];

    $token = $data["token"];

    if ($this->isCsrfTokenValid('aramex_shippement', $token)) {
      $order = $orderRepo->find($orderId);

      $user = $order->getUser();

      $addresses = $user->getAddresses()->toArray();

      $address = array_filter($addresses, fn (UserAddress $add) => $add->getIsDefault());

      if(!count($address)) {

         return $this->json(["error" => sprintf("No default address is set from %s addresses", count($addresses))], 422);
      }
 
      try {
        $shipement = $aramex->CreateShipments($address[0], $order);

        $trackingId = $shipement->getTrackingId();

        $attachement = $shipement->getShipmentAttachment();

        $attachementName = basename($attachement);

        $path = $this->getParameter('kernel.project_dir') . "/public/upload/aramex/" . $attachementName;

        $response = $httpClient->request('GET', $attachement);

        file_put_contents($path, $response->getContent());

        $aramexShipement = new AramexShipement();

        $aramexShipement->setTrackingId($trackingId)->setAttachement($attachementName);

        $order->setAramexShipement($aramexShipement);

        try {

          $aramexTracking = new EntityAramexTracking();

          $trackingResult = $aramexTrackingApi->trackOne($trackingId);

          $aramexTracking->setData($trackingResult)
            ->setUpdateCode($trackingResult["UpdateCode"])
            ->setWaybillNumber($trackingResult["WaybillNumber"]);

          $aramexShipement->setTracking($aramexTracking);

          $em->persist($order);

          $em->flush();
        } catch (Exception $err) {

          return $this->json(["error" => $err->getMessage()], 400);
        }

        return $this->json(["trackingId" => $shipement->getTrackingId()]);
      } catch (\Exception $e) {
        return $this->json(["error" => $e->getMessage()], 400);
      }
    }

    return $this->json(["error" => "CSRF TOKEN INVALID OR NOT SET"], 400);
  }


  /**
   * @Route("/aramex/track/shippement", name="track_aramex_shippements", methods={"POST"})
   */

  public function track(
    Request $request,
    AramexTracking $aramexTrackingApi,
    AramexTrackingRepository $repo,
    EntityManagerInterface $em
  ): Response {

    $data = $request->toArray();

    if (!isset($data['trackingIds']) || !is_array($data["trackingIds"])) {

      return $this->json(["Could not find or parse trackingIds array"], 400);
    }

    $trackingIds = (array) $data["trackingIds"];

    $updatedTrackings = [];

    try {

      $trackingResults = $aramexTrackingApi->trackMultiple($trackingIds);

      $HasMultipleResults = AramexHelper::isMultiDimensional($trackingResults);

      if ($HasMultipleResults) {

        foreach ($trackingResults as $result) {

          $trackingResult = (array) $result["Value"]["TrackingResult"];

          $waybillNumber = $trackingResult["WaybillNumber"];

          $trackingEntity = $repo->findOneBy(["waybillNumber" => $waybillNumber]);

          if (!$trackingEntity) {

            return $this->json(["error" => sprintf("Could not found this trackingId %", $waybillNumber)], 404);
          }

          array_push($updatedTrackings, $trackingEntity);

          $trackingEntity->setData($trackingResult)->setUpdateCode($trackingResult["UpdateCode"]);

          $em->persist($trackingEntity);
        }

        $em->flush();
      } else {

        $trackingResult = (array) $trackingResults["Value"]["TrackingResult"];

        $waybillNumber = $trackingResult["WaybillNumber"];

        $trackingEntity = $repo->findOneBy(["waybillNumber" => $waybillNumber]);

        if (!$trackingEntity) {

          return $this->json(["error" => sprintf("Could not found this trackingId %", $waybillNumber)], 404);
        }

        $trackingEntity->setData($trackingResult)->setUpdateCode($trackingResult["UpdateCode"]);

        array_push($updatedTrackings, $trackingEntity);

        $em->persist($trackingEntity);

        $em->flush();
      }
    } catch (Exception $e) {

      return $this->json(["error" => $e->getMessage()], 400);
    }

    return $this->json($updatedTrackings, 200, [], ["groups" => ["trackings:read"]]);
  }

  /**
   * @Route("/aramex/create/manifeste/{id}", name="aramex_create_manifest", methods={"GET"})
   */

  public function generateManifest(
    int $id,
    AramexPickUpRepository $repo,
    Twig $twig
  ) {

    $pickUp = $repo->find($id);

    if (!$pickUp) {

      throw $this->createNotFoundException("PickUp not found");
    }

    try {

      $options = new PdfOptions();

      $options->setIsRemoteEnabled(true)->setDpi(100);

      $domPdf = new Dompdf($options);

      $html = $twig->render("dashboard/aramex/manifeste.html.twig", [

        "pickUp" => $pickUp,
        "shippements" => $pickUp->getShippements()
      ]);

      $domPdf->setPaper('A4', 'portrait');

      $domPdf->loadHtml($html);

      $domPdf->render();

      $output = $domPdf->output();

      $projectDir = $this->getParameter('kernel.project_dir');

      $root_dir = $projectDir . "/public/upload/aramex/manifeste/";

      $unique_name = time() . uniqid(rand());

      $fileName = $unique_name . ".pdf";

      $file = $root_dir . $fileName;

      $filesystem = new Filesystem();

      $filesystem->dumpFile($file, $output);

      return new BinaryFileResponse($file);
    } catch (Exception $e) {

      return $this->json(["error" => "Unable to generate PDF manifest"]);
    }
  }
}
