<?php
/**
 * Copyright © MgtWizards. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */

namespace MgtWizards\Bestsellers\Helper;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Config constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry
    ) {
        $this->_storeManager = $storeManager;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }

    /**
     * @param $key
     * @param null $store
     * @return mixed
     */
    public function getConfigModule($key, $store = null)
    {
        return $this->scopeConfig->getValue(
            'mgtwizards_bestsellers/general/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Check category is it sale
     *
     * @return bool
     */
    public function isBestsellersCategory()
    {
        $category = $this->_coreRegistry->registry('current_category');
        if ($category
            && $category->getId() == $this->getConfigModule('category_id')
            && $this->getConfigModule('enabled')
        ) {
            return true;
        }
        return false;
    }

    /**
     * Return current website id.
     *
     * @return int
     */
    public function getCurrentWebsiteId()
    {
        return $this->_storeManager->getStore()->getWebsiteId();
    }
}
