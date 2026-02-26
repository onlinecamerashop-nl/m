<?php

namespace Bol\CheckoutViaBol\Model;

use Bol\CheckoutViaBol\Api\Data\CvbOrderInterface;
use Bol\CheckoutViaBol\Model\Cvb\ShipmentEventStatusEnum;
use Bol\CheckoutViaBol\Model\ResourceModel\CvbOrderCollection;
use Bol\CheckoutViaBol\Model\ResourceModel\CvbOrderResource;
use DateTimeInterface;
use Magento\Framework\Model\AbstractModel;

class CvbOrder extends AbstractModel implements CvbOrderInterface
{
    protected $_idFieldName = self::FIELD_ENTITY_ID;
    protected $_resourceName = CvbOrderResource::class;
    protected $_collectionName = CvbOrderCollection::class;

    public function setOrderId(int $orderId): static
    {
        return $this->setData(self::FIELD_ORDER_ID, $orderId);
    }

    public function getOrderId(): int
    {
        return (int)$this->getData(self::FIELD_ORDER_ID);
    }

    public function setCvbOrderReference(string $cvbOrderReference): static
    {
        return $this->setData(self::FIELD_CVB_ORDER_REFERENCE, $cvbOrderReference);
    }

    public function getCvbOrderReference(): string
    {
        return (string)$this->getData(self::FIELD_CVB_ORDER_REFERENCE);
    }

    public function setCvbSessionId(string $cvbSessionId): static
    {
        return $this->setData(self::FIELD_CVB_SESSION_ID, $cvbSessionId);
    }

    public function getCvbSessionId(): string
    {
        return (string)$this->getData(self::FIELD_CVB_SESSION_ID);
    }

    public function setShipmentEventStatus(ShipmentEventStatusEnum $shipmentEventStatus): static
    {
        return $this->setData(self::FIELD_SHIPMENT_EVENT_STATUS, $shipmentEventStatus->value);
    }

    public function getShipmentEventStatus(): ShipmentEventStatusEnum
    {
        $status = (int)$this->getData(self::FIELD_SHIPMENT_EVENT_STATUS);
        return ShipmentEventStatusEnum::from($status);
    }

    public function setShipmentEventTries(int $tries): static
    {
        return $this->setData(self::FIELD_SHIPMENT_EVENT_TRIES, $tries);
    }

    public function getShipmentEventTries(): int
    {
        return (int)$this->getData(self::FIELD_SHIPMENT_EVENT_TRIES);
    }

    public function setTryShipmentEventAgainAt(DateTimeInterface $dateTime): static
    {
        return $this->setData(self::FIELD_TRY_SHIPMENT_EVENT_AGAIN_AT, $dateTime->format(self::DATETIME_FORMAT));
    }

    public function getTryShipmentEventAgainAt(): ?DateTimeInterface
    {
        $tryAgain = $this->getData(self::FIELD_TRY_SHIPMENT_EVENT_AGAIN_AT);
        if (!$tryAgain) {
            return null;
        }

        return \DateTimeImmutable::createFromFormat(self::DATETIME_FORMAT, $tryAgain) ?: null;
    }

    public function setIsFillInSession(bool $isFillInSession): static
    {
        return $this->setData(self::FIELD_IS_FILL_IN_SESSION, $isFillInSession);
    }

    public function isFillInSession(): bool
    {
        return (bool)$this->getData(self::FIELD_IS_FILL_IN_SESSION);
    }
}
