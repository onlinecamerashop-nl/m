<?php
namespace ShopWhizzy\DigitalTheme\Block\Adminhtml\System\Form\Field;

use Magento\Framework\View\Element\Html\Select;
use Magento\Framework\View\Element\Context;

class WidthRenderer extends Select
{
    /**
     * @param \Magento\Framework\View\Element\Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
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
            $this->setOptions($this->_getWidthOptions());
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
    protected function _getWidthOptions()
    {
        $options = [
            ['value' => '', 'label' => __('-- Select --')],
            ['value' => 'fullwidth', 'label' => __('Fullwidth')],
            ['value' => 'auto', 'label' => __('Auto')],
        ];
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