<?php
/**
 * Copyright © MgtWizards. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */

namespace MgtWizards\Bestsellers\Model\Plugin;

/**
 * Class Layer
 */
class Layer
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
     * Layer constructor.
     * @param \MgtWizards\Bestsellers\Helper\Config $helper
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\CatalogRule\Model\ResourceModel\Rule $ruleResource
     */
    public function __construct(
        \MgtWizards\Bestsellers\Helper\Config $helper,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\CatalogRule\Model\ResourceModel\Rule $ruleResource
    ) {
        $this->helper = $helper;
        $this->httpContext = $httpContext;
        $this->ruleResource = $ruleResource;
    }

    /**
     * @param \Magento\Catalog\Model\Layer $subject
     * @param \Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection $collection
     */
    public function beforePrepareProductCollection(
        \Magento\Catalog\Model\Layer $subject,
        \Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection $collection
    ) {
        if ($this->helper->isBestsellersCategory()) {
            $groupRuleProducts = clone $collection->getSelect();
            $groupRuleProducts->reset()
                ->from($this->ruleResource->getTable('catalogrule_product'), ['product_id'])
                ->where(
                    'customer_group_id = ?',
                    $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP)
                )->where(
                    'website_id = ?',
                    $this->helper->getCurrentWebsiteId()
                )->distinct();

            $otherGroupsRuleProducts = clone $collection->getSelect();
            $otherGroupsRuleProducts->reset()
                ->from($this->ruleResource->getTable('catalogrule_product'), ['product_id'])
                ->where("product_id NOT IN ({$groupRuleProducts})")
                ->where('website_id = ?', $this->helper->getCurrentWebsiteId())
                ->distinct();

            $parentProducts = clone $collection->getSelect();
            $parentProducts->reset()
                ->from($this->ruleResource->getTable('catalog_product_relation'), ['parent_id'])
                ->where("child_id IN ({$groupRuleProducts})")
                ->distinct();

            $otherGroupsParentProducts = clone $collection->getSelect();
            $otherGroupsParentProducts->reset()
                ->from($this->ruleResource->getTable('catalog_product_relation'), ['parent_id'])
                ->where("child_id IN ({$otherGroupsRuleProducts})")
                ->where("parent_id NOT IN ({$parentProducts})")
                ->distinct();

            $collection->getSelect()
                ->where("(e.entity_id NOT IN ({$otherGroupsRuleProducts}))")
                ->where("(e.entity_id NOT IN ({$otherGroupsParentProducts}))");
        }
    }
}
