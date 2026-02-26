<?php
/**
 * Copyright © MgtWizards. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */

namespace MgtWizards\Bestsellers\Model\Plugin\UrlRewrite;

class Observer
{
    /**
     * @param \Magento\CatalogUrlRewrite\Observer\CategoryProcessUrlRewriteSavingObserver $subject
     * @param callable $proceed
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function aroundExecute(
        \Magento\CatalogUrlRewrite\Observer\CategoryProcessUrlRewriteSavingObserver $subject,
        callable $proceed,
        \Magento\Framework\Event\Observer $observer
    ) {
        try {
            $proceed($observer);
        } catch (\Magento\Framework\Exception\AlreadyExistsException $e) {
            /**
             * For ignoring errors if url_keys for products already exist
             */
        } catch (\Exception $e) {
            /**
             * For ignoring errors if url_keys for products already exist
             */
        }
    }
}
