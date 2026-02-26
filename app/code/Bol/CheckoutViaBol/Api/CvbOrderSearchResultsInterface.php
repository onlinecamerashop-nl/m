<?php

namespace Bol\CheckoutViaBol\Api;

use Bol\CheckoutViaBol\Api\Data\CvbOrderInterface;
use Magento\Framework\Api\SearchResultsInterface;

/**
 * This interface is purely here for the documentation in the docblocks.
 * Not super relevant now but if the BolCvbOrders get exposed via magento web api these docblocks are needed.
 */
interface CvbOrderSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return CvbOrderInterface[]
     */
    public function getItems();

    /**
     * @param CvbOrderInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items);
}
