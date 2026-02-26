<?php
/**
 * @author MgtWizards Team
 * @copyright Copyright (c) MgtWizards (https://shopwhizzy.com/)
 */
namespace MgtWizards\ShippingBar\Helper;

use Magento\OfflineShipping\Model\SalesRule\Rule;
use Magento\Store\Model\ScopeInterface;

/**
 * Data helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Config path to Enable for Free Shipping method
     */
    const XML_PATH_CARRIER_STATUS = 'mgtwizards_shipping_bar/general/carrier_status';

    /**
     * Config path to Enable for Cart Price Rule
     */
    const XML_PATH_SALES_RULE_STATUS = 'mgtwizards_shipping_bar/general/sales_rule_status';

    /**
     * Config path for using custom minimum amount
     */
    const XML_PATH_USE_MIN_AMT_ENABLED = 'mgtwizards_shipping_bar/general/use_min_amt';

    /**
     * Config path for minium amount for shipping
     */
    const XML_PATH_MIN_AMT = 'mgtwizards_shipping_bar/general/min_amt';

    /**
     * Config path to Show Progress Text
     */
    const XML_PATH_SHOW_PROGRESS_TEXT = 'mgtwizards_shipping_bar/design/show_progress_text';

    /**
     * Config path to Show Progress Graph
     */
    const XML_PATH_SHOW_PROGRESS_GRAPH = 'mgtwizards_shipping_bar/design/show_progress_graph';

    /**
     * Config path to Hide block if it empty
     */
    const XML_PATH_HIDE_IF_EMPTY = 'mgtwizards_shipping_bar/design/hide_if_empty';

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory
     */
    protected $ruleCollectionFactory;

    /**
     * @var bool|null
     */
    protected $hasFreeShipping = null;

    /**
     * @var float
     */
    protected $minimumAmount = null;

    /**
     * @var array|null
     */
    protected $totals = null;

    /**
     * @var \Magento\Tax\Api\TaxRateRepositoryInterface
     */
    protected $taxRateRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var string[] $freeShippingSalesRuleAttributes
     */
    protected $freeShippingSalesRuleAttributes;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory
     * @param \Magento\Tax\Api\TaxRateRepositoryInterface $taxRateRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param string[] $freeShippingSalesRuleAttributes
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory,
        \Magento\Tax\Api\TaxRateRepositoryInterface $taxRateRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        array $freeShippingSalesRuleAttributes = []
    ) {
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->taxRateRepository = $taxRateRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->freeShippingSalesRuleAttributes = $freeShippingSalesRuleAttributes;
    }

    /**
     * Checks if free shipping is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        $carrierEnabled = $this->scopeConfig->isSetFlag(self::XML_PATH_CARRIER_STATUS, ScopeInterface::SCOPE_STORE)
            && false !== $this->getCarrierMinimumAmount();

        return $carrierEnabled || $this->isSalesRuleEnabled();
    }

    /**
     * @return bool
     */
    public function isSalesRuleEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_SALES_RULE_STATUS, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function isShowProgressText()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_SHOW_PROGRESS_TEXT, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function isShowProgressGraph()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_SHOW_PROGRESS_GRAPH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function isHideIfEmpty()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_HIDE_IF_EMPTY, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Checks if free shipping is reached
     *
     * @return bool
     */
    public function hasFreeShipping()
    {
        if (null === $this->hasFreeShipping)
        {
            $this->prepareTotals();

            $freeShipping = $this->checkoutSession->getQuote()->getShippingAddress()->getFreeShipping();
            // beware '1' - is not same as true
            $this->hasFreeShipping = ($this->getMinimumAmount() === false)
                ? ($this->getSubtotal() > 0)
                : ($freeShipping === true || $this->getSubtotal() >= $this->getMinimumAmount());
        }

        return $this->hasFreeShipping;
    }

    /**
     * Returns amount remaining to free shipping
     *
     * @return int
     */
    public function getRemainingAmount()
    {
        if ($this->hasFreeShipping())
        {
            return 0;
        }
        else
        {
            return max(0, $this->getMinimumAmount() - $this->getSubtotal());
        }
    }

    /**
     * Returns progress percent
     *
     * @return int
     */
    public function getProgressPercent()
    {
        if ($this->hasFreeShipping() || (!$this->getMinimumAmount() && $this->getSubtotal()))
        {
            return 100;
        }
        else
        {
            $value = ($this->getMinimumAmount()) ? 100 * $this->getSubtotal() / $this->getMinimumAmount() : 0;
            return (int)min(max(0, $value), 100);
        }
    }

    /**
     * Returns min subtotal amount for freeshipping configured in admin
     *
     * @return float|false
     */
    public function getMinimumAmount()
    {
        if (null == $this->minimumAmount)
        {
            $values = [];
            $minCarrier = $this->getCarrierMinimumAmount();
            if ($minCarrier > 0)
            {
                $values[] = $minCarrier;
            }
            $minCartRule = $this->getCartRuleMinimumAmount();
            if ($minCartRule > 0)
            {
                $values[] = $minCartRule;
            }
            $this->minimumAmount = $values ? min($values) : false;
        }
        if ($this->minimumAmount === false)
        {
            if ($this->isCustomMinAmtEnabled())
            {
                $this->minimumAmount = $this->getCustomMinAmtForFreeshipping();
            }
        }

        return $this->minimumAmount;
    }

    /**
     * Returns current cart subtotal amount
     *
     * @return float
     */
    public function getSubtotal()
    {
        return min($this->getSubtotalWithoutDiscount(), $this->getSubtotalWithDiscount());
    }

    /**
     * Returns current cart subtotal amount without discounts
     *
     * @return float
     */
    public function getSubtotalWithoutDiscount()
    {
        $this->prepareTotals();

        return isset($this->totals['subtotal']) ? $this->totals['subtotal']->getValue() : 0;
    }

    /**
     * Returns current cart subtotal amount with discounts
     *
     * @return float
     */
    public function getSubtotalWithDiscount()
    {
        $this->prepareTotals();

        if ($this->scopeConfig->isSetFlag(self::XML_PATH_SALES_RULE_STATUS))
        {
            if ($this->checkoutSession->getQuote()->isVirtual())
            {
                $address = $this->checkoutSession->getQuote()->getBillingAddress();
            }
            else
            {
                $address = $this->checkoutSession->getQuote()->getShippingAddress();
            }

            return $address->getGrandTotal();
        }
        else
        {
            return isset($this->totals['grand_total']) ? $this->totals['grand_total']->getValue() : 0;
        }
    }

    /**
     * Returns free shipping carrier min order amount if enabled
     *
     * @return float|false
     */
    public function getCarrierMinimumAmount()
    {
        $carrierEnabled = $this->scopeConfig->isSetFlag(self::XML_PATH_CARRIER_STATUS);
        $active = $this->scopeConfig->isSetFlag('carriers/freeshipping/active');
        $min = $this->scopeConfig->getValue('carriers/freeshipping/free_shipping_subtotal');

        if ($carrierEnabled && $active && $min > 0)
        {
            return $min;
        }
        else
        {
            return false;
        }
    }

    /**
     * Searches shopping cart price rules for a simple free shipping rule and returns min subtotal amount if found
     *
     * Cart rule must only contain single condition - subtotal greater or equals, and free shipping action enabled.
     * If multiple such rules are found, minimum amount is returned.
     *
     * @param $website
     * @param $customerGroup
     * @return float|false
     */
    public function getCartRuleMinimumAmount($website = null, $customerGroup = null)
    {
        if (!$this->isSalesRuleEnabled())
        {
            return false;
        }

        $values = [];

        /** @var \Magento\SalesRule\Model\ResourceModel\Rule\Collection $rules */
        $rules = $this->ruleCollectionFactory->create();
        $rules->addWebsiteFilter($website ?: $this->storeManager->getStore()->getWebsiteId())
            ->addCustomerGroupFilter($customerGroup ?: $this->customerSession->getCustomerGroupId())
            ->addIsActiveFilter(true);

        foreach ($rules as $rule)
        {
            $conditions = $rule->getConditions()->asArray();
            if ($rule->getSimpleFreeShipping() == Rule::FREE_SHIPPING_ADDRESS
             && $rule->getCouponType() == \Magento\SalesRule\Model\Rule::COUPON_TYPE_NO_COUPON
             && $conditions['aggregator'] == 'all'
             && !empty($conditions['conditions'])
             && count($conditions['conditions']) == 1
             && in_array($conditions['conditions'][0]['attribute'], $this->freeShippingSalesRuleAttributes)
             && $conditions['conditions'][0]['operator'] == '>='
             && $conditions['conditions'][0]['value'] > 0
            )
            {
                $values[] = $conditions['conditions'][0]['value'];
            }
        }
        //get first rate
        $searchResults = $this->taxRateRepository->getList($this->searchCriteriaBuilder->create());
        $taxRates = $searchResults->getItems();
        /** @var \Magento\Tax\Api\Data\TaxRateInterface $taxRate */
        $taxRate = array_shift($taxRates);
        if ($taxRate)
        {
            $rate = ($taxRate->getRate() + 100) / 100;
        }
        else
        {
            $rate = 1;
        }
        $value = $values ? min($values) : 0;

        return $value * $rate;
    }

    /**
     * Ensures quote totals are collected and returns totals
     *
     * @return array
     */
    protected function prepareTotals()
    {
        if (null === $this->totals)
        {
            $quote = $this->checkoutSession->getQuote();
            $quote->collectTotals();

            $this->totals = $quote->getTotals();
        }

        return $this->totals;
    }

    /**
     * Returns system config for custom min amt usage
     *
     * @return bool
     */
    public function isCustomMinAmtEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_USE_MIN_AMT_ENABLED);
    }

    /**
     * Returns the minimum amount configured for free shipping
     *
     * @return mixed
     */
    public function getCustomMinAmtForFreeshipping()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_MIN_AMT);
    }
}
