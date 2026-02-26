<?php

namespace Bol\CheckoutViaBol\Api\Data;

use Bol\CheckoutViaBol\Model\Cvb\ShipmentEventStatusEnum;
use DateTimeInterface;

interface CvbOrderInterface
{
    public const FIELD_ENTITY_ID = 'entity_id';
    public const FIELD_ORDER_ID = 'order_id';
    public const FIELD_CVB_ORDER_REFERENCE = 'cvb_order_reference';
    public const FIELD_CVB_SESSION_ID = 'cvb_session_id';
    public const FIELD_SHIPMENT_EVENT_STATUS = 'shipment_event_status';
    public const FIELD_SHIPMENT_EVENT_TRIES = 'shipment_event_tries';
    public const FIELD_TRY_SHIPMENT_EVENT_AGAIN_AT = 'try_shipment_event_again_at';
    public const FIELD_IS_FILL_IN_SESSION = 'is_fill_in_session';

    public const DATETIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @param int $orderId
     *
     * @return $this
     */
    public function setOrderId(int $orderId): static;

    /**
     * @return int
     */
    public function getOrderId(): int;

    /**
     * @param string $cvbOrderReference
     *
     * @return $this
     */
    public function setCvbOrderReference(string $cvbOrderReference): static;

    /**
     * @return string
     */
    public function getCvbOrderReference(): string;

    /**
     * @param string $cvbSessionId
     *
     * @return $this
     */
    public function setCvbSessionId(string $cvbSessionId): static;

    /**
     * @return string
     */
    public function getCvbSessionId(): string;

    /**
     * @param ShipmentEventStatusEnum $shipmentEventStatus
     *
     * @return $this
     */
    public function setShipmentEventStatus(ShipmentEventStatusEnum $shipmentEventStatus): static;

    /**
     * @return ShipmentEventStatusEnum
     */
    public function getShipmentEventStatus(): ShipmentEventStatusEnum;

    /**
     * @param int $tries
     *
     * @return $this
     */
    public function setShipmentEventTries(int $tries): static;

    /**
     * @return int
     */
    public function getShipmentEventTries(): int;

    /**
     * @param DateTimeInterface $dateTime
     *
     * @return $this
     */
    public function setTryShipmentEventAgainAt(DateTimeInterface $dateTime): static;

    /**
     * @return DateTimeInterface|null
     */
    public function getTryShipmentEventAgainAt(): ?DateTimeInterface;

    /**
     * @param bool $isFillInSession
     *
     * @return $this
     */
    public function setIsFillInSession(bool $isFillInSession): static;

    /**
     * @return bool
     */
    public function isFillInSession(): bool;
}
