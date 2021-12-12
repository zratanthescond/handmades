<?php

namespace App\Controller\Promoter;

use App\Entity\Promoter;
use App\Form\Promoter\PromoterAccountType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
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
     * @Route("/promoter/histories", name="discount_code_histories")
     */
     
    public function discountHistories()
    {
         
        $discountCodes = $this->promoter->getDiscountCodes();

        return $this->render("promoter/discount_codes/histories.html.twig", [

            "discountCodes" => $discountCodes
        ]);
    }

    /**
     * @Route("/promoter/account", name="promoter_account")
     */

     public function account()
     {
         $form = $this->createForm(PromoterAccountType::class, $this->promoter);

         $form->handleRequest($this->request);

         if($form->isSubmitted() && $form->isValid()) {

             $this->em->persist($this->promoter);

             $this->em->flush();
         }

         return $this->render("promoter/account.html.twig", [

            "form" => $form->createView()
         ]);
     }
}