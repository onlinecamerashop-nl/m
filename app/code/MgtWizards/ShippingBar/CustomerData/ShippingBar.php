<?php
/**
 * @author MgtWizards Team
 * @copyright Copyright (c) 2016-2017 MgtWizards (http://shopwhizzy.com/)
 */
namespace MgtWizards\ShippingBar\CustomerData;

/**
 * Shipping bar section for customer
 *
 * Returns current free shipping status and indicators for shipping bar
 */
class ShippingBar implements \Magento\Customer\CustomerData\SectionSourceInterface
{
    /**
     * @var \MgtWizards\ShippingBar\Helper\Data
     */
    protected $shippingBarHelper;

    /**
     * @var \Magento\Checkout\Helper\Data
     */
    protected $checkoutHelper;

    /**
     * @param \MgtWizards\ShippingBar\Helper\Data $shippingBarHelper
     * @param \Magento\Checkout\Helper\Data $checkoutHelper
     */
    public function __construct(
        \MgtWizards\ShippingBar\Helper\Data $shippingBarHelper,
        \Magento\Checkout\Helper\Data $checkoutHelper
    ) {
        $this->shippingBarHelper = $shippingBarHelper;
        $this->checkoutHelper = $checkoutHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {
        if (!$this->shippingBarHelper->isEnabled()) {
            return [];
        }

        return [
            'data_loaded' => true,
            'has_free_shipping' => $this->shippingBarHelper->hasFreeShipping(),
            'remaining_amount' => $this->checkoutHelper->formatPrice($this->shippingBarHelper->getRemainingAmount()),
            'progress_percent' => $this->shippingBarHelper->getProgressPercent(),
        ];
    }
}
