<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class PayementController extends AbstractController
{

    /**
     * @Route("/gpgdata", name="gpg_data", methods={"POST"})
     */

    public function generate(Request $request): Response
    {

        $data = $request->toArray();

        if(!isset($data["amount"])) {

            return $this->json(["error" => "Amount field is required"], 400);
        }

        $numSite = "MAR920";

        $password = "gh#khW62";

        $orderId = date('ymdHis');

        $Devise = 'TND';

        $Amount = $data["amount"];

        $signature = sha1($numSite.$password.$orderId.$Amount.$Devise);

        return $this->json([

            "NumSite" => $numSite,
            "Password" => MD5($password),
            "orderID" => $orderId,
            "signature" => $signature
        ]);

    }

}