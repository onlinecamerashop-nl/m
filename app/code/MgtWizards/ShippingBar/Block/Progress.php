<?php
/**
 * @author MgtWizards Team
 * @copyright Copyright (c) 2016-2017 MgtWizards (http://www.convert.no/)
 */
namespace MgtWizards\ShippingBar\Block;

/**
 * Free Shipping Bar
 */
class Progress extends \Magento\Framework\View\Element\Template
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
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        if (!$this->shippingBarHelper->isEnabled())
        {
            return '';
        }

        return parent::_toHtml();
    }
}
