<?php

namespace App\Controller\Promoter;

use App\Entity\Promoter;
use App\Entity\PromoterWithdrawalRequest;
use App\Event\PromoterWithdrawalRequestEvent;
use App\Form\Promoter\PromoterWithdrawalRequestType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class PromoterWithdrawalRequestController extends AbstractController
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
    * @Route("/promoter/withdrawals", name="promoter_withdrawals")
    */

   public function withdrawalsRequests(): Response
   {

      $withdrawals  = $this->promoter->getWithdrawalsRequests();

      return $this->render("promoter/withdrawal/index.html.twig",  [

         "withdrawals" => $withdrawals
      ]);
   }


   /**
    * @Route("/promoter/withdrawal/request", name="promoter_withdrawal_request")
    */

   public function request(EventDispatcherInterface $dispatcher): Response
   {

      $withdrawal = new PromoterWithdrawalRequest();

      $form = $this->createForm(PromoterWithdrawalRequestType::class, $withdrawal);

      $form->handleRequest($this->request);

      if ($form->isSubmitted() && $form->isValid()) {

         $this->promoter->addWithdrawalsRequest($withdrawal);

         $event = new PromoterWithdrawalRequestEvent($withdrawal);

         $dispatcher->dispatch($event, PromoterWithdrawalRequestEvent::EVENT_NAME);
 
         $withdrawalAmount = $withdrawal->getAmount();

         $this->em->persist($this->promoter);

         $this->em->flush();

         $format = new \NumberFormatter("fr_FR", \NumberFormatter::CURRENCY);

         $format->setAttribute(\NumberFormatter::FRACTION_DIGITS, 3);

         $amount = $format->formatCurrency($withdrawalAmount, "TND");

         $this->addFlash("success", sprintf("Demande de retrait de %s effectuée avec succéss", $amount));

         return $this->redirectToRoute("promoter_withdrawals");
      }

      return $this->render("promoter/withdrawal/request.html.twig",  [

         "form" => $form->createView()
      ]);
   }
}
