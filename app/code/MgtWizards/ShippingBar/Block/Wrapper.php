<?php
/**
 * @author MgtWizards Team
 * @copyright Copyright (c) 2016-2017 MgtWizards (http://shopwhizzy.com/)
 */
namespace MgtWizards\ShippingBar\Block;

/**
 * Free Shipping Bar Wrapper
 */
class Wrapper extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \MgtWizards\ShippingBar\Helper\Data
     */
    protected $shippingBarHelper;

    /**
     * @param \MgtWizards\ShippingBar\Helper\Data $shippingBarHelper
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \MgtWizards\ShippingBar\Helper\Data $shippingBarHelper,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->shippingBarHelper = $shippingBarHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function isHideIfEmpty()
    {
        return $this->shippingBarHelper->isHideIfEmpty();
    }

}
