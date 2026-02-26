<?php
/**
 * Copyright © MgtWizards. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */

namespace MgtWizards\Bestsellers\Model\Plugin\Search;

/**
 * Class IndexBuilder
 */
class IndexBuilder
{
    /**
     * Helper instance
     *
     * @var \MgtWizards\Bestsellers\Helper\Config
     */
    protected $helper;
    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;
    /**
     * Rule Resource
     *
     * @var Resource
     */
    protected $ruleResource;
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * IndexBuilder constructor.
     * @param \MgtWizards\Bestsellers\Helper\Config $helper
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\CatalogRule\Model\ResourceModel\Rule $ruleResource
     */
    public function __construct(
        \MgtWizards\Bestsellers\Helper\Config $helper,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\CatalogRule\Model\ResourceModel\Rule $ruleResource
    ) {
        $this->helper = $helper;
        $this->_storeManager = $storeManager;
        $this->httpContext = $httpContext;
        $this->ruleResource = $ruleResource;
    }

    /**
     * @param \Magento\CatalogSearch\Model\Search\IndexBuilder $subject
     * @param \Magento\Framework\DB\Select $select
     * @return \Magento\Framework\DB\Select
     */
    public function afterBuild(
        \Magento\CatalogSearch\Model\Search\IndexBuilder $subject,
        \Magento\Framework\DB\Select $select
    ) {
        if ($this->helper->isBestsellersCategory()) {
            $groupRuleProducts = clone $select;
            $groupRuleProducts->reset()
                ->from($this->ruleResource->getTable('catalogrule_product'), ['product_id'])
                ->where(
                    'customer_group_id = ?',
                    $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP)
                )->where(
                    'website_id = ?',
                    $this->helper->getCurrentWebsiteId()
                )->distinct();

            $otherGroupsRuleProducts = clone $select;
            $otherGroupsRuleProducts->reset()
                ->from($this->ruleResource->getTable('catalogrule_product'), ['product_id'])
                ->where("product_id NOT IN ({$groupRuleProducts})")
                ->where('website_id = ?', $this->helper->getCurrentWebsiteId())
                ->distinct();

            $parentProducts = clone $select;
            $parentProducts->reset()
                ->from($this->ruleResource->getTable('catalog_product_relation'), ['parent_id'])
                ->where("child_id IN ({$groupRuleProducts})")
                ->distinct();

            $otherGroupsParentProducts = clone $select;
            $otherGroupsParentProducts->reset()
                ->from($this->ruleResource->getTable('catalog_product_relation'), ['parent_id'])
                ->where("child_id IN ({$otherGroupsRuleProducts})")
                ->where("parent_id NOT IN ({$parentProducts})")
                ->distinct();

            $select->where("(search_index.entity_id NOT IN ({$otherGroupsRuleProducts}))")
                ->where("(search_index.entity_id NOT IN ({$otherGroupsParentProducts}))");
        }

        return $select;
    }
}
