<?php

namespace Bol\CheckoutViaBol\Service;

use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface;

class RedirectService
{
    public function __construct(
        private readonly UrlService       $urlService,
        private readonly RedirectFactory  $redirectFactory,
        private readonly ManagerInterface $messageManager,
    ) {
    }

    public function redirectToCart(): ResultInterface
    {
        return $this->createRedirect($this->urlService->getCartUrl());
    }

    public function redirectToCheckout(): ResultInterface
    {
        return $this->createRedirect($this->urlService->getCheckoutUrl());
    }

    public function redirectToCvbOrderError(string $errorMessage = null): ResultInterface
    {
        if ($errorMessage) {
            $this->messageManager->addErrorMessage($errorMessage);
        }

        return $this->createRedirect($this->urlService->getCvbOrderErrorUrl());
    }

    public function redirectToCheckoutSuccess(): ResultInterface
    {
        return $this->createRedirect($this->urlService->getCheckoutSuccessUrl());
    }

    public function redirectToPaymentStep(): ResultInterface
    {
        return $this->createRedirect($this->urlService->getPaymentUrl());
    }

    private function createRedirect(string $url): ResultInterface
    {
        return $this->redirectFactory->create()
            ->setUrl($url);
    }
}
