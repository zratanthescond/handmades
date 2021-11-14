<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\ProductReview;
use App\Form\OrderReviewType;
use App\Form\ProductReviewType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductReviewController extends AbstractController
{
    /**
     * @Route("/product/review/{id}", name="product_review")
     */
    public function index(int $id, EntityManagerInterface $em, Request $request): Response
    {

        $order = $em->getRepository(Order::class)->find($id);

        if (!$order) {

            throw $this->createNotFoundException();
        }

        $reviews = $order->getProductReviews();

        // check if user has already evaluated products 

        if (!$reviews->isEmpty()) {

            //dd($reviews);

            return $this->render("product_review/reviews.html.twig", [

                "reviews" => $reviews
            ]);
        }

        $productsOrder = $order->getProducts()->toArray();

        foreach ($productsOrder as $p) {

            $product = $p->getProduct();

            $productReview = (new ProductReview())->setProduct($product);

            $order->addProductReview($productReview);
        }

        $formBuilder = $this->createFormBuilder($order)
            ->add('productReviews', CollectionType::class, [

                'entry_type' => ProductReviewType::class,
                "label" => false
            ])

            ->add("submit", SubmitType::class, [

                "label" => "Envoyer"
            ]);

        if (!$order->getOrderReview()) {

            $formBuilder->add("orderReview", OrderReviewType::class, [

                "label" => false
            ]);
        }

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($order);

            $em->flush();

            return $this->redirect($request->getUri());
        }

        // dd($form);

        return $this->render('product_review/index.html.twig', [
            'form' => $form->createView(),
            "products" => $productsOrder

        ]);
    }
}
