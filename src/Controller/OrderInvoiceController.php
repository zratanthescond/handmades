<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\UserAddress;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options as PdfOptions;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Twig\Environment as Twig;
use Doctrine\ORM\EntityManagerInterface;


class OrderInvoiceController extends AbstractController
{

    /**
     * @Route("/order/invoice/{id}", name="order_invoice", methods={"GET"})
     */

    public function index(
        int $id,
        OrderRepository $repo,
        Twig $twig,
        EntitymanagerInterface $em
    ): Response {

        $order = $repo->find($id);

        if (!$order) {

            throw $this->createNotFoundException("Order not found");
        }

        $client = $order->getUser();

        $addresses = array_filter($client->getAddresses()->toArray(), function (UserAddress $address) {

            return $address->getIsDefault();
        });

        $defaultAddress = count($addresses) ? $addresses[0] : null;

        $freeDelivery = $order->getTotal() >= 99;

        $discount = 0;

        $discount = +$order->getTotal() - $order->getSubtotal();

        if ($freeDelivery) {

            $discount += 7;
        } else {

            $discount -= 7;
        }

        $html = $twig->render(
            "dashboard/order/invoice.html.twig",

            [
                "order" => $order,
                "defaultAddress" => $defaultAddress,
                "discount" => $discount

            ]
        );

        $options = new PdfOptions();

        $options->setIsRemoteEnabled(true);

        $domPdf = new Dompdf($options);

        $domPdf->setPaper('A3', 'portrait');

        $domPdf->loadHtml($html);

        $domPdf->render();

        $output = $domPdf->output();

        $projectDir = $this->getParameter('kernel.project_dir');

        $root_dir = $projectDir . "/public/upload/invoice/";

        $unique_name = time() . uniqid(rand());

        $fileName = $unique_name . ".pdf";

        $file = $root_dir . $fileName;

        $order->setInvoice($fileName);

        $em->persist($order);
        
        $em->flush();;

        $filesystem = new Filesystem();

        $filesystem->dumpFile($file, $output);

        return new BinaryFileResponse($file);
    }
}
