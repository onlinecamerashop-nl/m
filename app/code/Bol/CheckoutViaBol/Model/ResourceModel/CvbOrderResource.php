<?php

namespace Bol\CheckoutViaBol\Model\ResourceModel;

use Bol\CheckoutViaBol\Api\Data\CvbOrderInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CvbOrderResource extends AbstractDb
{
    public const TABLE_NAME = 'bol_cvb_order';

    /** @noinspection MagicMethodsValidityInspection */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, CvbOrderInterface::FIELD_ENTITY_ID);
    }

    /**
     * @param int $magentoOrderId
     *
     * @return int|null
     * @noinspection PhpDocMissingThrowsInspection
     */
    public function getEntityIdByMagentoOrderId(int $magentoOrderId): ?int
    {
        $orderIdField = CvbOrderInterface::FIELD_ORDER_ID;
        $select       = $this->getConnection()->select();
        /** @noinspection PhpUnhandledExceptionInspection */
        $select->from($this->getMainTable());
        $select->columns([CvbOrderInterface::FIELD_ENTITY_ID]);
        $select->where("$orderIdField = ?", $magentoOrderId);

        return $select->query()->fetchColumn() ?: null;
    }

    /**
     * @param int $entityId
     *
     * @return int|null
     * @noinspection PhpDocMissingThrowsInspection
     */
    public function getMagentoOrderIdByEntityId(int $entityId): ?int
    {
        $entityIdField = CvbOrderInterface::FIELD_ENTITY_ID;
        $select        = $this->getConnection()->select();
        /** @noinspection PhpUnhandledExceptionInspection */
        $select->from($this->getMainTable());
        $select->columns([CvbOrderInterface::FIELD_ORDER_ID]);
        $select->where("$entityIdField = ?", $entityId);

        return $select->query()->fetchColumn() ?: null;
    }
}
