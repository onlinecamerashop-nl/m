<?php
namespace ShopWhizzy\DigitalTheme\Block\Adminhtml\System\Form\Field;

use Magento\Framework\View\Element\Html\Select;
use Magento\Framework\View\Element\Context;
use MgtWizards\Base\Helper\Data as IconHelper;

class IconRenderer extends Select
{
    protected $_iconHelper;

    /**
     * @param \Magento\Framework\View\Element\Context $context
     * @param \MgtWizards\Base\Helper\Data $iconHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        IconHelper $iconHelper,
        array $data = []
    ) {
        $this->_iconHelper = $iconHelper;
        parent::__construct($context, $data);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->getOptions())
        {
            $this->setOptions($this->_getIconOptions());
        }
        $this->setClass('select admin__control-select');
        $this->setName($this->getName());
        return parent::_toHtml();
    }

    /**
     * Get icon options for dropdown
     *
     * @return array
     */
    protected function _getIconOptions()
    {
        $options = [
            ['value' => '', 'label' => __('-- Select Icon --')]
        ];
        $iconNames = $this->_iconHelper->getThemeIconNames();

        foreach ($iconNames as $iconName)
        {
            $options[] = [
                'value' => $iconName,
                'label' => $iconName
            ];
        }
        return $options;
    }

    /**
     * Set input name
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }
}