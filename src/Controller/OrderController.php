<?php


namespace App\Controller;


use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Form\OrderType;
use App\Service\OrderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use WayForPay\SDK\Collection\ProductCollection;
use WayForPay\SDK\Credential\AccountSecretCredential;
use WayForPay\SDK\Credential\AccountSecretTestCredential;
use WayForPay\SDK\Domain\Client;
use WayForPay\SDK\Domain\Product as WayForPayProduct;
use WayForPay\SDK\Exception\ApiException;
use WayForPay\SDK\Wizard\CheckWizard;
use WayForPay\SDK\Wizard\PurchaseWizard;

class OrderController extends AbstractController
{

    /**
     * @Route("/add-to-cart/{id}", name="order_add_to_cart")
     */
    public function addToCart(Product $product, OrderService $orderService, Request $request)
    {
        $orderService->add($product, 1, $this->getUser());

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
            $orderService->save($item->getCart(), $this->getUser());
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

    /**
     * @Route("/cart/order", name="order_make_order")
     */
    public function makeOrder(OrderService $orderService, Request $request)
    {
        $order = $orderService->getOrder();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $orderService->makeOrder($order);

            return $this->redirectToRoute('order_success', ['id'=>$order->getId()]);
        }

        return $this->render('order/make_order.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/order/success/{id}", name="order_success")
     */
    public function orderSuccess(Order $order)
    {
        $form = $this->createPaymentForm($order);

        return $this->render('order/success.html.twig', [
            'form'=> $form,
        ]);

    }

    private function createPaymentForm(Order $order)
    {
        // Use test credential or yours
        $credential = new AccountSecretTestCredential();
        $credential = new AccountSecretCredential($_ENV['WAYFORPAY_ACCOUNT'], $_ENV['WAYFORPAY_SECRET']);

        $productCollection = new ProductCollection();

        foreach ($order->getItems() as $item){
            $productCollection->add(new WayForPayProduct($item->getName(), $item->getPrice()/100, $item->getCount()));
        }



        $form = PurchaseWizard::get($credential)
            ->setOrderReference($order->getId() . '_' . time())
            ->setAmount($order->getAmount()/100)
            ->setCurrency('UAH')
            ->setOrderDate($order->getOrderedAt())
            ->setMerchantDomainName($this->generateUrl('default', [], UrlGeneratorInterface::ABSOLUTE_URL))
            ->setClient(new Client(
                $order->getFirstName(),
                $order->getLastName(),
                $order->getEmail(),
                null,
                null,
                $order->getUser()? $order->getUser()->getId() : null,
                $order->getAddress()
            ))
            ->setProducts($productCollection)
            ->setReturnUrl($this->generateUrl('order_payment', ['id' => $order->getId()], UrlGeneratorInterface::ABSOLUTE_URL))
            ->getForm()
            ->getAsString('Оплатить');

        return $form;
    }

    /**
     * @Route("/order/payment/{id}", name="order_payment")
     */
    public function payment(Order $order, Request $request, EntityManagerInterface $entityManager)
    {
        $credential = new AccountSecretCredential($_ENV['WAYFORPAY_ACCOUNT'], $_ENV['WAYFORPAY_SECRET']);

        try{
        $response = CheckWizard::get($credential)
            ->setOrderReference($request->request->get('orderReference'))
            ->getRequest()
            ->send();
        } catch (ApiException $exception){
            $error = $exception->getMessage();
            $response = null;
        }

        if ($response && $response->getReason()->isOK()){
            $order->setPaidAt(new \DataTime());
            $entityManager->flush();
            $error = null;

        } else {
           $error = $error ?? $response->getReason()->getMessage();
        }

        return $this->render('order/payment_status.html.twig', [
            'order'=> $order,
            'error'=> $error,
        ]);

    }
}