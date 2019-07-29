<?php


namespace App\Controller;


use App\Entity\Product;
use App\Service\OrderService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{

    /**
     * @Route("/add-to-cart/{id}", name="order_add_to_cart")
     */
    public function addToCart(Product $product, OrderService $orderService)
    {
        $orderService->add($product, 1);

        return $this->redirectToRoute('default');
    }

    public function headerCart(OrderService $orderService)
    {
        $order = $orderService->getOrder();

        return $this->render('order/header_cart.html.twig', [
            'order' => $order,
        ]);
    }
}