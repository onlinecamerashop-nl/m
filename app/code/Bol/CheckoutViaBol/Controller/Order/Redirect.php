<?php

namespace Bol\CheckoutViaBol\Controller\Order;

use Bol\CheckoutViaBol\Exception\CvbApiException;
use Bol\CheckoutViaBol\Model\CvbText;
use Bol\CheckoutViaBol\Service\CvbService;
use Bol\CheckoutViaBol\Service\CvbOrderService;
use Bol\CheckoutViaBol\Service\RedirectService;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Sales\Api\Data\OrderInterface;

class Redirect implements HttpGetActionInterface
{
    public function __construct(
        private readonly Context         $context,
        private readonly Session         $checkoutSession,
        private readonly CvbService      $cvbService,
        private readonly CvbOrderService $cvbOrderService,
        private readonly RedirectService $redirectService,
    ) {
    }

    /**
     * Order place has been successful, which means the order is registered in magento.
     * Now to either send the customer to the success page if he has a valid cvb session which may use bnpl,
     * or initialize a new cvb bnpl session and send the customer to the bol platform to finalize the order.
     *
     * @return ResultInterface
     * @throws NotFoundException
     */
    public function execute(): ResultInterface
    {
        $order = $this->checkoutSession->getLastRealOrder();
        if (!$order->getId()) {
            throw new NotFoundException(__('Not found'));
        }

        /** @see \Magento\Framework\Session\SessionManager::__call for how ->getCvbSession is handled */
        /** @noinspection PhpUndefinedMethodInspection */
        $cvbSessionId = $this->checkoutSession->getCvbSessionId();
        if (!$cvbSessionId) {
            return $this->bnplRedirect($order);
        }

        try {
            $bolOrderId = $this->cvbOrderService->placeCvbOrder($cvbSessionId, $order);
            if ($bolOrderId === null) {
                # Session has expired or something is wrong with it, redirect to bol to finalize the order
                return $this->bnplRedirect($order);
            }
            # Purge the cvb session reference after a successful place order call
            $this->checkoutSession->unsCvbSessionId();
        } catch (CvbApiException $e) {
            return $this->redirectService->redirectToCvbOrderError(CvbText::CVB_ORDER_ERROR_MESSAGE);
        }

        return $this->redirectService->redirectToCheckoutSuccess();
    }

    private function bnplRedirect(OrderInterface $order): ResultInterface
    {
        $bnplSession = $this->cvbService->createCvbPaymentOnlySession($order);
        return $this->context->getResultRedirectFactory()
            ->create()
            ->setUrl($bnplSession['redirectUrl']);
    }
}
