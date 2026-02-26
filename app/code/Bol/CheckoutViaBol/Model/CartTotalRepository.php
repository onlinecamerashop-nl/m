<?php

namespace Bol\CheckoutViaBol\Model;

use Bol\CheckoutViaBol\Service\CvbService;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\CartTotalRepositoryInterface;
use Magento\Quote\Api\Data\TotalsInterface as QuoteTotalsInterface;
use Magento\Quote\Api\Data\TotalsInterfaceFactory;
use Magento\Quote\Model\Cart\Totals\ItemConverter;
use Magento\Quote\Model\Cart\TotalsConverter;

class CartTotalRepository implements CartTotalRepositoryInterface
{
    public function __construct(
        private readonly TotalsInterfaceFactory  $totalsFactory,
        private readonly CartRepositoryInterface $quoteRepository,
        private readonly DataObjectHelper        $dataObjectHelper,
        private readonly TotalsConverter         $totalsConverter,
        private readonly ItemConverter           $itemConverter
    ) {
    }

    /**
     * An almost copy paste from magento, the issue is that magento assumes that the cart is still active, this is not
     * true at this point. We need the totals to communicate a correct price view to BOL. From an order context
     * we cannot see which totals contributed to the grand total. The usual magento totals should be there
     * however it could be that some custom totals are also applied, sadly the order model does not expose
     * which totals have been applied (at least to our knowledge).
     *
     * @param $cartId
     *
     * @return QuoteTotalsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     *
     * @see CvbService::getCartBasketData (this is called from there)
     */
    public function get($cartId)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->get($cartId); // Used get instead of getActive
        $quote->collectTotals();

        if ($quote->isVirtual()) {
            $addressTotalsData = $quote->getBillingAddress()->getData();
            $addressTotals     = $quote->getBillingAddress()->getTotals();
        } else {
            $addressTotalsData = $quote->getShippingAddress()->getData();
            $addressTotals     = $quote->getShippingAddress()->getTotals();
        }
        unset($addressTotalsData[ExtensibleDataInterface::EXTENSION_ATTRIBUTES_KEY]);

        /** @var QuoteTotalsInterface $quoteTotals */
        $quoteTotals = $this->totalsFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $quoteTotals,
            $addressTotalsData,
            QuoteTotalsInterface::class
        );
        $items            = array_map([$this->itemConverter, 'modelToDataObject'], $quote->getAllVisibleItems());
        $calculatedTotals = $this->totalsConverter->process($addressTotals);
        $quoteTotals->setTotalSegments($calculatedTotals);
        $quoteTotals->setCouponCode($quote->getCouponCode()); // Use coupon code directly
        $quoteTotals->setItems($items);
        $quoteTotals->setItemsQty($quote->getItemsQty());
        $quoteTotals->setBaseCurrencyCode($quote->getBaseCurrencyCode());
        $quoteTotals->setQuoteCurrencyCode($quote->getQuoteCurrencyCode());
        return $quoteTotals;
    }
}
