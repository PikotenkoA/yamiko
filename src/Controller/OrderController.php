<?php


namespace App\Controller;


use App\Entity\OrderItem;
use App\Entity\Product;
use App\Service\OrderService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{

    /**
     * @Route("/add-to-cart/{id}", name="order_add_to_cart")
     */
    public function addToCart(Product $product, OrderService $orderService, Request $request)
    {
        $orderService->add($product, 1);
        if ($request->isXmlHttpRequest()) {
            return $this->headerCart($orderService);
        }

        return $this->redirectToRoute('default');
    }

    public function headerCart(OrderService $orderService)
    {
        $order = $orderService->getOrder();

        return $this->render('order/header_cart.html.twig', [
            'order' => $order,
        ]);
    }

    /**
     * @Route("/cart}", name="order_cart")
     */
    public function cart(OrderService $orderService)
    {
        $order = $orderService->getOrder();

        return $this->render('order/cart.html.twig', [
            'order' => $order,
        ]);
    }

    /**
     * @Route("/cart/{id}/count", name="order_set_count")
     */
    public function setCount(OrderItem $item, OrderService $orderService, Request $request)
    {
        $count = $request->request->getInt('count');
        $order = $orderService->getOrder();

        if ($count > 0 && $item->getCart() === $order) {
            $item->setCount($count);
            $orderService->save($item->getCart());
        }

        return $this->render('order/_cart_table.html.twig', [
            'order' => $order,
        ]);
    }

    /**
     * @Route("/cart/{id}/delete", name="order_delete_item")
     */
    public function deleteItem(OrderItem $item, OrderService $orderService)
    {
        $order = $orderService->getOrder();

        if ($item->getCart() === $order) {
            $orderService->deleteItem($item);
        }

        return $this->render('order/_cart_table.html.twig',
        ['order' => $order,
        ]);
    }
}