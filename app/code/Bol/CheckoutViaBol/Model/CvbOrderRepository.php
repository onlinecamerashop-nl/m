<?php

namespace Bol\CheckoutViaBol\Model;

use Bol\CheckoutViaBol\Api\CvbOrderRepositoryInterface;
use Bol\CheckoutViaBol\Api\CvbOrderSearchResultsInterface;
use Bol\CheckoutViaBol\Api\Data\CvbOrderInterface;
use Bol\CheckoutViaBol\Api\Data\CvbOrderInterfaceFactory;
use Bol\CheckoutViaBol\Model\ResourceModel\CvbOrderCollection;
use Bol\CheckoutViaBol\Model\ResourceModel\CvbOrderCollectionFactory;
use Bol\CheckoutViaBol\Model\ResourceModel\CvbOrderResource;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class CvbOrderRepository implements CvbOrderRepositoryInterface
{
    public function __construct(
        private readonly CvbOrderInterfaceFactory     $cvbOrderFactory,
        private readonly CvbOrderResource             $cvbOrderResource,
        private readonly CvbOrderCollectionFactory    $collectionFactory,
        private readonly CollectionProcessorInterface $collectionProcessor,
    ) {
    }

    /**
     * @param int $entityId
     *
     * @return CvbOrderInterface
     * @throws NoSuchEntityException
     */
    public function get(int $entityId): CvbOrderInterface
    {
        /** @var CvbOrderInterface $cvbOrder */
        $cvbOrder = $this->cvbOrderFactory->create();
        $this->cvbOrderResource->load($cvbOrder, $entityId);
        if (!$cvbOrder->getEntityId()) {
            throw new NoSuchEntityException(__('Bol order with id: %1 does not exist', $entityId));
        }

        return $cvbOrder;
    }

    /**
     * @param int $magentoOrderId
     *
     * @return CvbOrderInterface
     * @throws NoSuchEntityException
     */
    public function getByMagentoOrderId(int $magentoOrderId): CvbOrderInterface
    {
        $entityId = $this->cvbOrderResource->getEntityIdByMagentoOrderId($magentoOrderId);
        if (!$entityId) {
            throw new NoSuchEntityException(
                __('Bol order with magento order id: %1 does not exist', $magentoOrderId)
            );
        }

        return $this->get($entityId);
    }

    /**
     * @param SearchCriteria $criteria
     *
     * @return CvbOrderSearchResultsInterface
     */
    public function getList(SearchCriteria $criteria): CvbOrderSearchResultsInterface
    {
        /** @var CvbOrderCollection $collection */
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($criteria, $collection);
        return $collection;
    }

    /**
     * @param CvbOrderInterface $cvbOrder
     *
     * @return void
     * @noinspection PhpDocMissingThrowsInspection
     */
    public function save(CvbOrderInterface $cvbOrder): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->cvbOrderResource->save($cvbOrder);
    }

    /**
     * @param CvbOrderInterface $cvbOrder
     *
     * @return void
     * @noinspection PhpDocMissingThrowsInspection
     */
    public function delete(CvbOrderInterface $cvbOrder): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->cvbOrderResource->delete($cvbOrder);
    }

    /**
     * @param int $entityId
     *
     * @return void
     */
    public function deleteById(int $entityId): void
    {
        try {
            $this->delete($this->get($entityId));
        } catch (NoSuchEntityException $e) {
            // If it is not there then there is nothing to delete
        }
    }
}
