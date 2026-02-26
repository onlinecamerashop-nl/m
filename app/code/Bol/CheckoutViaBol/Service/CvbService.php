<?php

declare(strict_types=1);

namespace Bol\CheckoutViaBol\Service;

use Bol\CheckoutViaBol\Exception\CvbApiException;
use Bol\CheckoutViaBol\Model\CartTotalRepository;
use Bol\CheckoutViaBol\Model\Cvb\DeliveryTypeEnum;
use Bol\CheckoutViaBol\Model\Cvb\EventEnum;
use Bol\CheckoutViaBol\Model\Logger;
use Magento\Framework\Exception\StateException;
use Magento\Quote\Api\CartItemRepositoryInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Api\Data\TotalSegmentInterface;
use Magento\Quote\Api\Data\TotalsItemInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Tax\Model\Config as TaxConfig;

class CvbService
{

    public function __construct(
        private readonly CvbApi                  $cvbApi,
        private readonly CartTotalRepository     $cartTotalRepository,
        private readonly CartRepositoryInterface $cartRepository,
        private readonly UrlService              $urlService,
        private readonly TaxConfig               $taxConfig,
        private readonly Logger                  $logger,
    ) {
    }

    /**
     * @param CartInterface $cart
     *
     * @return array
     */
    public function createCvbSession(CartInterface $cart): array
    {
        $this->logger->logCreateFillInSession($cart);
        # Uri's are paths otherwise a bad request error comes up.
        $cartData = [
            'basket'    => $this->getCartBasketData($cart),
            'returnUri' => "/{$this->urlService->getCvbSessionCheckoutPath((string)$cart->getId())}",
            'cancelUri' => "/{$this->urlService->getCartPath()}",
        ];

        if ($customerIp = $cart->getData('remote_ip')) {
            $cartData['customerIpAddress'] = $customerIp;
        }

        return $this->cvbApi->createCvbSession($cartData);
    }

    /**
     * @param OrderInterface $order
     *
     * @return array
     */
    public function createCvbPaymentOnlySession(OrderInterface $order): array
    {
        $this->logger->logCreateBnplSession($order);
        $orderData = $this->mapOrderData($order);
        # Uri's are paths otherwise a bad request error comes up.
        $orderData['returnUri'] = "/{$this->urlService->getCvbOrderPlacePath($order)}";
        $orderData['cancelUri'] = "/{$this->urlService->getCvbOrderErrorPath()}";

        return $this->cvbApi->createCvbSession($orderData);
    }

    /**
     * @param                $cvbSessionId
     * @param OrderInterface $order
     *
     * @return array
     *
     * @throws CvbApiException
     */
    public function createOrder($cvbSessionId, OrderInterface $order): array
    {
        $this->logger->logCreateOrder($cvbSessionId, $order);
        $orderData                   = $this->mapOrderData($order);
        $orderData['sessionId']      = $cvbSessionId;
        $orderData['orderReference'] = $order->getIncrementId();

        return $this->cvbApi->createOrder($orderData);
    }

    /**
     * @param string $bolOrderReference
     *
     * @return array
     *
     * @throws CvbApiException
     */
    public function createShipmentEvent(string $bolOrderReference): array
    {
        return $this->cvbApi->createOrderEvent(
            $bolOrderReference,
            [
                'event' => EventEnum::SHIPPED->value
            ]
        );
    }

    public function createRefundEvent(
        string $bolOrderReference,
        int    $amountInCents,
        string $reason = null
    ): array {

        $eventData = [
            'event'         => EventEnum::REFUND->value,
            'amountInCents' => $amountInCents,
        ];

        if ($reason) {
            $eventData['reason'] = $reason;
        }

        return $this->cvbApi->createOrderEvent($bolOrderReference, $eventData);
    }

    /**
     * Both createBnplOnlySession and create order use the same base data.
     *
     * @param OrderInterface $order
     *
     * @return array
     */
    private function mapOrderData(OrderInterface $order): array
    {
        $orderData = [
            'basket'          => $this->getOrderBasketData($order),
            'billingAddress'  => $this->getAddressData($order->getBillingAddress()),
            'shippingAddress' => $this->getAddressData($order->getShippingAddress())
        ];

        $customerIp = $order->getRemoteIp();
        if ($customerIp) {
            $orderData['customerIpAddress'] = $customerIp;
        }

        return $orderData;
    }

    /**
     * Cvb basket data consists of products and other 'items' such as delivery costs, fee's etc etc
     * In magento terms: all products and all quote totals except subtotal, that information is already in the products
     *
     * @param Quote $cart
     *
     * @return array
     */
    private function getCartBasketData(CartInterface $cart): array
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $totals = $this->cartTotalRepository->get($cart->getId());

        # Get the product items
        $cartProductItems = array_values(array_map(
            fn (TotalsItemInterface $cartItemTotal) => $this->getBasketItemFromCartItem($cartItemTotal, $cart),
            $totals->getItems(),
        ));

        # Subtotal is not included as this information is presented in the form of products
        $totalsToSkip = ['subtotal', 'grand_total'];
        if ($this->taxConfig->priceIncludesTax()) {
            $totalsToSkip[] = 'tax';
        }

        try {
            $shippingMethod    = $cart->getShippingAddress()?->getShippingMethod() ?: null;
            $hasShippingMethod = (bool)$shippingMethod;
        } catch (StateException $e) {
            // State exception is thrown when there is no shipping address (and thus no shipping method)
            $hasShippingMethod = false;
        }

        if (!$hasShippingMethod) {
            $totalsToSkip[] = 'shipping';
        }

        $cartTotals = array_filter(
            $totals->getTotalSegments(),
            static function (TotalSegmentInterface $totalSegment) use ($totalsToSkip) {
                return !in_array($totalSegment->getCode(), $totalsToSkip);
            }
        );

        # Get additional costs, use array_values to lose the key association
        $cartTotalItems = array_values(array_map(
            fn (TotalSegmentInterface $totalSegment) => $this->getBasketItemFromTotalSegment($totalSegment),
            $cartTotals,
        ));

        // Tax can be 0 so filter that out if that is the case, there might also be totals registered by thrid party
        // integrations, filter these out as well if they are not relevant.
        // Always display shipping if it is not filtered out above
        $items = array_filter(
            array_merge($cartProductItems, $cartTotalItems),
            static function ($item) {
                return abs($item['priceInCents']) > 0 || $item['itemType'] === DeliveryTypeEnum::DELIVERY->value;
            }
        );

        $totalPriceInCents = $this->getPriceInCents((float)$totals->getGrandTotal());
        return [
            'items'             => array_values($items),
            'totalPriceInCents' => $totalPriceInCents,
        ];
    }

    private function getOrderBasketData(OrderInterface $order): array
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->getCartBasketData($this->cartRepository->get($order->getQuoteId()));
    }

    /**
     * @param TotalsItemInterface $cartItem
     *
     * @return array
     */
    private function getBasketItemFromCartItem(TotalsItemInterface $cartItemTotal, CartInterface $cart): array
    {
        $itemId   = $this->resolveCartItemId($cartItemTotal, $cart);
        $itemName = $this->resolveCartItemName($cartItemTotal);

        $totalValue = $this->taxConfig->priceIncludesTax()
            ? $cartItemTotal->getRowTotalInclTax()
            : $cartItemTotal->getRowTotal();

        return [
            'id'           => $itemId,
            'productTitle' => $itemName,
            'quantity'     => $cartItemTotal->getQty(),
            'priceInCents' => $this->getPriceInCents((float)$totalValue),
            'itemType'     => DeliveryTypeEnum::PRODUCT->value
        ];
    }

    private function resolveCartItemId(TotalsItemInterface $cartItemTotal, CartInterface $cart): string
    {
        $id        = $cartItemTotal->getItemId();
        $cartItems = $cart->getItems() ?: $cart->getAllVisibleItems();

        $cartItem = array_filter(
            $cartItems,
            static fn (CartItemInterface $item) => $item->getId() === $id
        );

        $cartItem = reset($cartItem);
        // $cartItem should always be set as a TotalItemsInterface is associated with a cart item (i.e. a product)
        // The sku should have variant info in it.
        return $cartItem ? $cartItem->getSku() : $this->resolveCartItemName($cartItemTotal);
    }

    private function resolveCartItemName(TotalsItemInterface $cartItemTotal): string
    {
        $name = $cartItemTotal->getName();
        try {
            $options = json_decode($cartItemTotal->getOptions(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return $name;
        }

        if (empty($options)) {
            return $name;
        }

        $options = array_map(
            static fn (array $option) => sprintf('%s: %s', $option['label'], $option['value']),
            $options
        );

        $optionString = implode(', ', $options);
        return sprintf('%s (%s)', $name, $optionString);
    }

    private function getBasketItemFromTotalSegment(TotalSegmentInterface $totalSegment): array
    {
        return [
            'id'           => $totalSegment->getCode(),
            'productTitle' => $totalSegment->getTitle(),
            'quantity'     => 1,
            'priceInCents' => $this->getPriceInCents((float)$totalSegment->getValue()),
            'itemType'     => $totalSegment->getCode() === 'shipping'
                ? DeliveryTypeEnum::DELIVERY->value
                : DeliveryTypeEnum::OTHER->value
        ];
    }

    private function getPriceInCents(float $price): int
    {
        return (int)round((float)$price * 100);
    }

    private function getAddressData(OrderAddressInterface $address): array
    {
        $addressData = [
            'firstName'   => $address->getFirstname(),
            'infix'       => $address->getMiddlename(),
            'lastName'    => $address->getLastname(),
            'countryCode' => $address->getCountryId(),
            'postalCode'  => $address->getPostcode(),
            'city'        => $address->getCity(),
            'region'      => $address->getRegion(),
            'companyName' => $address->getCompany(),
            'attentionOf' => '', # Is this sir or miss?
        ];
        # Map the magento street to cvb street
        $cvbStreetKeys = [
            'streetName',
            'houseNumber',
            'houseNumberExtension',
            'addressExtraInformation',
        ];
        $street        = array_pad($address->getStreet(), 4, '');
        $street        = array_combine($cvbStreetKeys, $street);

        return array_merge($addressData, $street);
    }
}
