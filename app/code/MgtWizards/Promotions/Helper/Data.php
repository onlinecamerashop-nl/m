<?php
/**
 * Copyright © MgtWizards. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */

namespace MgtWizards\Promotions\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;
use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Model\CategoryFactory;

class Data extends AbstractHelper
{
    const XML_PATH_IS_ENABLE = 'promotionpro/general/enabled';
    const XML_PATH_NEW_PRODUCTS_CATEGORY = 'promotionpro/general/category_ids';

    protected $_scopeConfig;
    protected $logger;
    protected $_productCollectionFactory;
    protected $categoryLinkManagement;
    protected $categoryFactory;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param LoggerInterface $logger
     * @param ProductCollectionFactory $productCollectionFactory
     * @param CategoryLinkManagementInterface $categoryLinkManagement
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger,
        ProductCollectionFactory $productCollectionFactory,
        CategoryLinkManagementInterface $categoryLinkManagement,
        CategoryFactory $categoryFactory
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->logger = $logger;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->categoryLinkManagement = $categoryLinkManagement;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * Check if the module is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->_scopeConfig->isSetFlag(
            self::XML_PATH_IS_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get promotion product category ID
     *
     * @return int|null
     * @throws \InvalidArgumentException
     */
    public function promotionProductCategory(): ?int
    {
        $categoryId = $this->_scopeConfig->getValue(
            self::XML_PATH_NEW_PRODUCTS_CATEGORY,
            ScopeInterface::SCOPE_STORE
        );

        if (empty($categoryId) || !is_numeric($categoryId))
        {
            throw new \InvalidArgumentException('Invalid or missing category ID in configuration.');
        }

        return (int)$categoryId;
    }

    /**
     * Synchronize products in the promotions category
     *
     * @return void
     * @throws \InvalidArgumentException
     */
    public function syncPromotionsCategory(): void
    {
        if (!$this->isEnabled())
        {
            $this->logger->info('Promotions category update is disabled.');
            return;
        }

        try
        {
            $categoryId = $this->promotionProductCategory();

            // Validate category existence
            $category = $this->categoryFactory->create()->load($categoryId);
            if (!$category->getId())
            {
                throw new \InvalidArgumentException("Category with ID $categoryId does not exist.");
            }

            // Get products currently in the category
            $existingProducts = $this->getExistingCategoryProducts($categoryId);

            // Remove all existing products from the category
            foreach ($existingProducts as $productSku)
            {
                try
                {
                    $this->categoryLinkManagement->assignProductToCategories($productSku, []);
                    $this->logger->info("Removed product SKU $productSku from category $categoryId.");
                }
                catch (\Exception $e)
                {
                    $this->logger->error("Failed to remove product SKU $productSku: " . $e->getMessage());
                }
            }

            // Get products with active special prices
            $collection = $this->getOnSaleProductCollection();

            $assignedCount = 0;
            foreach ($collection as $product)
            {
                try
                {
                    $productSku = $product->getSku();
                    $this->categoryLinkManagement->assignProductToCategories($productSku, [$categoryId]);
                    $this->logger->info("Assigned product SKU $productSku to category $categoryId.");
                    $assignedCount++;
                }
                catch (\Exception $e)
                {
                    $this->logger->error("Failed to assign product SKU {$product->getSku()}: " . $e->getMessage());
                }
            }

            $this->logger->info("Assigned $assignedCount products to promotions category $categoryId.");
        }
        catch (\Exception $e)
        {
            $this->logger->error("Error updating promotions category: " . $e->getMessage());
            throw $e; // Re-throw to allow callers to handle the exception
        }
    }

    /**
     * Get products currently assigned to the category
     *
     * @param int $categoryId
     * @return array
     */
    protected function getExistingCategoryProducts(int $categoryId): array
    {
        $category = $this->categoryFactory->create()->load($categoryId);
        $productCollection = $category->getProductCollection()->addAttributeToSelect('sku');
        $skus = [];
        foreach ($productCollection as $product)
        {
            $skus[] = $product->getSku();
        }
        return $skus;
    }

    /**
     * Get collection of products with active special prices
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function getOnSaleProductCollection()
    {
        $tomorrow = (new \DateTime())->modify('+1 day')->setTime(0, 0, 0);
        $today = (new \DateTime())->setTime(0, 0, 0);

        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect(['sku', 'special_price', 'special_from_date', 'special_to_date'])
            ->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
            ->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addStoreFilter()
            ->addAttributeToSort('minimal_price', 'asc');

        // Filter for special price validity
        $collection->addAttributeToFilter([
            [
                'attribute' => 'special_from_date',
                'null' => true,
            ],
            [
                'attribute' => 'special_from_date',
                'lteq' => $today->format('Y-m-d H:i:s'),
            ],
        ], '', 'left');

        $collection->addAttributeToFilter([
            [
                'attribute' => 'special_to_date',
                'null' => true,
            ],
            [
                'attribute' => 'special_to_date',
                'gteq' => $tomorrow->format('Y-m-d H:i:s'),
            ],
        ], '', 'left');

        // Ensure special price is active and less than regular price
        $collection->getSelect()->where('price_index.final_price < price_index.price');

        return $collection;
    }
}