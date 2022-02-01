<?php

namespace App\Controller\Promoter;

use App\Entity\DiscountCode;
use App\Entity\Promoter;
use App\Form\Promoter\PromoterAccountType;
use App\Repository\PromoterEarningRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class PromoterController extends AbstractController
{

    private Promoter $promoter;

    private Request $request;

    private EntityManagerInterface $em;

    public function __construct(Security $security, RequestStack $requestStack, EntityManagerInterface $em)
    {
        $this->promoter = $security->getUser();

        $this->request = $requestStack->getCurrentRequest();

        $this->em = $em;
    }

    /**
     * @Route("/promoter/discount-code/list", name="promoter_discount_code_list")
     */

    public function discountCodesList(): Response
    {

        $discountCodes = $this->promoter->getDiscountCodes();

        return $this->render("promoter/discount_codes/index.html.twig", [

            "discountCodes" => $discountCodes
        ]);
    }

    /**
     * @Route("/promoter/discount-code/{id}", name="promoter_discount_code")
     */

    public function discountCode(DiscountCode $discountCode, PromoterEarningRepository $repo)
    {

        $promoterDiscount = $discountCode->getPromoter();

        if (!$promoterDiscount) {

            return $this->redirectToRoute("promoter_discount_code_list");
        }

        if ($promoterDiscount !== $this->promoter) {

            throw $this->createAccessDeniedException();
        }

        $earnings = $repo->findBy(["discountCode" => $discountCode], ["createdAt" => "DESC"]);

        return $this->render("promoter/discount_codes/single.html.twig", [

            "discountCode" => $discountCode,
            "earnings" => $earnings
        ]);
    }

    /**
     * @Route("/promoter/account", name="promoter_account")
     */

    public function account(): Response
    {
        $form = $this->createForm(PromoterAccountType::class, $this->promoter);

        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->em->persist($this->promoter);

            $this->em->flush();
        }

        return $this->render("promoter/account.html.twig", [

            "form" => $form->createView()
        ]);
    }
}
