<?php

namespace MgtWizards\Bestsellers\Cron;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory as BestSellersCollectionFactory;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

class UpdateBestsellersCategory
{
    protected $_categoryFactory;
    protected $_productCollectionFactory;
    protected $_catalogLayer;
    protected $_configHelper;
    protected $_localeDate;
    protected $_storeManager;
    protected $_processedCategories = [];
    protected $_registry;
    protected $ruleResource;
    protected $_resource;
    protected $eavConfig;
    protected $_bestSellersCollectionFactory;
    protected $grouped;
    protected $configurable;

    public function __construct(
        CategoryFactory $categoryFactory,
        CollectionFactory $productCollectionFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \MgtWizards\Bestsellers\Helper\Config $configHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        \Magento\CatalogRule\Model\ResourceModel\Rule $ruleResource,
        ResourceConnection $resource,
        \Magento\Eav\Model\Config $eavConfig,
        BestSellersCollectionFactory $bestSellersCollectionFactory,
        Grouped $grouped,
        Configurable $configurable
    ) {
        $this->_configHelper = $configHelper;
        $this->_categoryFactory = $categoryFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_catalogLayer = $layerResolver->get();
        $this->_localeDate = $localeDate;
        $this->_storeManager = $storeManager;
        $this->_registry = $registry;
        $this->ruleResource = $ruleResource;
        $this->_resource = $resource;
        $this->eavConfig = $eavConfig;
        $this->_bestSellersCollectionFactory = $bestSellersCollectionFactory;
        $this->grouped = $grouped;
        $this->configurable = $configurable;
    }

    public function getTablename($tableName)
    {
        $connection  = $this->_resource->getConnection();
        $tableName   = $connection->getTableName($tableName);

        return $tableName;
    }

    public function execute()
    {
        $stores = $this->_storeManager->getStores();
        foreach ($stores as $store) {
            if ($this->_configHelper->getConfigModule('enabled', $store->getId())) {
                $this->_storeManager->setCurrentStore($store->getId());

                $category_id = $this->_configHelper->getConfigModule('category_id', $store->getId());
                $category = $this->_categoryFactory->create()->load($category_id);
                $this->setCategoryToRegistry($category);

                //reset category products
                //$this->resetCategoryProducts($category);

                $bestSellers = $this->getBestsellersProducts();
                $productIds = $this->getPrepareFinalIds($bestSellers);
                if (empty($productIds)) {
                    return null;
                }
                //var_dump($productIds);
                //exit;

                $this->assignProducts($category, $productIds);
            }
        }
    }

    public function flatten(array $array)
    {
    }

    public function getParentProductsIds($childIds, $storeId)
    {
        $connection = $this->_resource->getConnection();
        $select = $connection->select()->from(
            ['cpe' => $this->_resource->getTableName('catalog_product_entity')],
            'entity_id'
        )->join(
            ['relation' => $this->_resource->getTableName('catalog_product_relation')],
            'relation.parent_id = cpe.entity_id'
        )->where('relation.child_id IN (?)', $childIds);

        if ($this->_configHelper->getConfigModule('show_with_images', $storeId)) {
            $imageAttribute = $this->eavConfig->getAttribute(\Magento\Catalog\Model\Product::ENTITY, 'small_image');
            $linkFieldName = $imageAttribute->getEntity()->getLinkField();
            $select->joinLeft(
                ['at_small_image_default' => $imageAttribute->getBackendTable()],
                "at_small_image_default.{$linkFieldName} = cpe.{$linkFieldName} 
                AND at_small_image_default.attribute_id = {$imageAttribute->getId()}
                AND at_small_image_default.store_id = 0"
            )
                ->where('at_small_image_default.value <> ?', 'no_selection')
                ->where('at_small_image_default.value IS NOT NULL');
        }

        $parentIds = $connection->fetchCol($select);
        $productIds = $parentIds ? array_unique(array_merge($parentIds, $childIds)) : $childIds;
        return $productIds;
    }

    protected function getBestsellersProducts2()
    {
        $bestSellers = $this->_bestSellersCollectionFactory->create()->setModel('Magento\Catalog\Model\Product')->setPeriod('month');

        return $bestSellers;
    }

    protected function getBestsellersProducts()
    {
        $so = $this->getTablename('sales_order');
        $soi = $this->getTablename('sales_order_item');
        $select = "SELECT
        year_ordered, 
        product_type,
        sku,
        product_id,
        name,
        sum(qty_ordered) as qty,
        sum(row_total) as total
from (

SELECT 
    YEAR(so.created_at) AS year_ordered, 
    -- order_id, 
    product_type, sku, product_id, name, qty_ordered, price, row_total


   FROM `$so` AS so
   INNER JOIN `$soi` AS si ON si.order_id=so.entity_id
        AND (so.state != 'canceled' )
 ORDER BY so.created_at desc

) stat

group by stat.year_ordered, stat.product_type, stat.sku, stat.name
order by year_ordered desc, total desc
LIMIT 50";
        $rows = $this->_resource->getConnection()->fetchAll($select);

        return $rows;
    }

    public function getPrepareFinalIds($array)
    {
        $productIds = [];
        $connection = $this->_resource->getConnection();
        $select = $connection->select()->from(
            ['cpi' => $this->_resource->getTableName('catalog_product_entity')],
            'entity_id'
        );
        $allids = $connection->fetchCol($select);

        foreach ($array as $product) {
            $productId = $product['product_id'];
            $parentIdsGroup  = $this->grouped->getParentIdsByChild($productId);
            $parentIdsConfig = $this->configurable->getParentIdsByChild($productId);

            if (!empty($parentIdsGroup)) {
                foreach ($parentIdsGroup as $parentIdGroup) {
                    if (in_array($parentIdGroup, $allids)) {
                        $productIds[] = $parentIdsGroup;
                    }
                }
            } elseif (!empty($parentIdsConfig)) {
                foreach ($parentIdsGroup as $parentIdGroup) {
                    if (in_array($parentIdsGroup, $allids)) {
                        $productIds[] = $parentIdsConfig[0];
                    }
                }
            } else {
                $productIds[] = $productId;
            }
        }

        $return = [];
        array_walk_recursive($productIds, function ($a) use (&$return) {
            $return[] = $a;
        });
        return array_unique($return);
    }

    protected function preparePostedProducts($productIds, $products = [], $sort = 1)
    {
        $x = 1;
        foreach ($productIds as $productId) {
            $products[$productId] = $x;
            $x++;
        }

        return $products;
    }

    protected function setCategoryToRegistry($category)
    {
        if ($this->_registry->registry('current_category')) {
            $this->_registry->unregister('current_category');
        }
        $this->_registry->register('current_category', $category);
    }

    protected function isCategoryProcessed($categoryId)
    {
        if (array_key_exists($categoryId, $this->_processedCategories)) {
            return true;
        }
        $this->_processedCategories[$categoryId] = $categoryId;

        return false;
    }

    protected function assignProducts2(Category $category, array $productIds = [])
    {
        if ($this->isCategoryProcessed($category->getId())) {
            $assignedProducts = $category->getProductsPosition();
            $productIds = $this->mergeProductsArray($productIds, $assignedProducts);
        }
        $category->setPostedProducts($productIds);
        $category->save();

        return $this;
    }

    protected function assignProducts(Category $category, array $productIds = [])
    {
        //reset
        $category->setPostedProducts([]);
        $category->save();

        $cat_id = $category->getId();
        $ccp = $this->getTablename('catalog_category_product');
        foreach ($productIds as $id) {
            $query = "INSERT  INTO `$ccp` (`category_id`,`product_id`,`position`) VALUES ($cat_id, $id, 0)";
            $run = $this->_resource->getConnection()->query($query);
        }

        return $this;
    }

    protected function resetCategoryProducts(Category $category)
    {
        $category->setPostedProducts([]);
        $category->save();

        return $this;
    }

    protected function mergeProductsArray($productIds, $assignedProducts)
    {
        foreach ($productIds as $productId => $value) {
            if (
                !array_key_exists($productId, $assignedProducts)
                || $assignedProducts[$productId] > $value
            ) {
                $assignedProducts[$productId] = $value;
            }
        }

        return $assignedProducts;
    }
}
