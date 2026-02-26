<?php

namespace Bol\CheckoutViaBol\Model\ResourceModel;

use Bol\CheckoutViaBol\Api\CvbOrderSearchResultsInterface;
use Bol\CheckoutViaBol\Model\CvbOrder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class CvbOrderCollection extends AbstractCollection implements CvbOrderSearchResultsInterface
{
    /** @noinspection MagicMethodsValidityInspection */
    protected function _construct()
    {
        $this->_init(
            CvbOrder::class,
            CvbOrderResource::class
        );
    }

    /**
     * @param array $items
     *
     * @return $this
     */
    public function setItems(array $items): static
    {
        return $this;
    }

    public function getSearchCriteria()
    {
        return null;
    }

    public function setSearchCriteria(SearchCriteriaInterface $searchCriteria)
    {
        return $this;
    }

    public function getTotalCount(): int
    {
        return count($this->getItems());
    }

    public function setTotalCount($totalCount): static
    {
        // Why ?
        return $this;
    }
}
