<?php

namespace App\Controller;

use App\Entity\DeliveryType;
use App\Entity\DiscountCode;
use App\Entity\Order;
use App\Entity\PayementTransaction;
use App\Entity\Product;
use App\Entity\ProductOrder;
use App\Entity\User;
use App\Event\OrderIsPlacedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

class MakeOrderController extends AbstractController
{

    private $deliveryRepo;

    private $userRepo;

    private $productRepo;

    private $discountCodeRepo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->deliveryRepo = $em->getRepository(DeliveryType::class);

        $this->userRepo = $em->getRepository(User::class);

        $this->productRepo = $em->getRepository(Product::class);

        $this->discountCodeRepo = $em->getRepository(DiscountCode::class);
    }

    public function __invoke(Request $request, EventDispatcherInterface $dispatcher)
    {
        $data = $request->toArray();

        $order = new Order();

        $delivery = $this->deliveryRepo->find($data["delivery"]["id"]);

        $user = $this->userRepo->find($data["user"]["id"]);

        $items = $data["items"];

        foreach($items as $item) {

              $product = $this->productRepo->find($item["product"]["id"]);

              $productOrder = new ProductOrder();

              $productOrder->setProduct($product)->setQty($item["qty"]);

              $order->addProduct($productOrder);
        }

        $rewardPointsToConsume = $data["rewardPointsToConsume"];

        if(isset($data["discountCodeId"])) {

             $discountCode = $this->discountCodeRepo->find($data["discountCodeId"]);

             if($discountCode) {

                 $order->setDiscountCode($discountCode);
             }
        }

        $order->setDelivery($delivery)->setUser($user)
        ->setSubtotal($data["subtotal"])
        ->setTotal($data["total"])
        ->setRewardPointsToConsume($rewardPointsToConsume);

        if(isset($data["note"])) {

              $order->setNote($data["note"]);
        }

        if(isset($data["paymentRef"])) {

              $transaction = (new PayementTransaction())
              ->setRef($data["paymentRef"])->setType("GPG");
              $order->setPayementTransaction($transaction)
              ->setStatus(0);
            }

        $event = new OrderIsPlacedEvent($order);

        $dispatcher->dispatch($event, OrderIsPlacedEvent::EVENT_NAME);

        return $order;
            
    }
}
