<?php

namespace Bol\CheckoutViaBol\Api;

use Bol\CheckoutViaBol\Api\Data\CvbOrderInterface;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Exception\NoSuchEntityException;

interface CvbOrderRepositoryInterface
{
    /**
     * @param int $entityId
     *
     * @return CvbOrderInterface
     * @throws NoSuchEntityException
     */
    public function get(int $entityId): CvbOrderInterface;

    /**
     * @param SearchCriteria $criteria
     *
     * @return CvbOrderSearchResultsInterface
     */
    public function getList(SearchCriteria $criteria): CvbOrderSearchResultsInterface;

    /**
     * @param CvbOrderInterface $cvbOrder
     *
     * @return void
     */
    public function save(CvbOrderInterface $cvbOrder): void;

    /**
     * @param int $magentoOrderId
     *
     * @return CvbOrderInterface
     * @throws NoSuchEntityException
     */
    public function getByMagentoOrderId(int $magentoOrderId): CvbOrderInterface;

    /**
     * @param CvbOrderInterface $cvbOrder
     *
     * @return void
     */
    public function delete(CvbOrderInterface $cvbOrder): void;

    /**
     * @param int $entityId
     *
     * @return void
     */
    public function deleteById(int $entityId): void;
}
