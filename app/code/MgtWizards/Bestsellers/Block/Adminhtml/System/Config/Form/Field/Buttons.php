<?php
/**
 * Copyright © MgtWizards. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace MgtWizards\Bestsellers\Block\Adminhtml\System\Config\Form\Field;

class Buttons extends \Magento\Config\Block\System\Config\Form\Field
{
    const BUTTON_TEMPLATE = 'system/config/button/button.phtml';

    protected $_buttonsCollection = [];

    /**
     * Set template to itself
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate(static::BUTTON_TEMPLATE);
        }
        return $this;
    }
    /**
     * Render button
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        // Remove scope label
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }
    /**
     * Get the button and scripts contents
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->_addButton(
            'button-to-regenerate',
            $this->_urlBuilder->getUrl('category_pro/action/regenerateProducts'),
            'Run',
            'mgtwizards-button-left'
        );

        return $this->_toHtml();
    }

    /**
     * @return array
     */
    public function getButtons()
    {
        return $this->_buttonsCollection;
    }

    /**
     * @param $id
     * @param $ajaxUrl
     * @param $label
     * @param null $class
     */
    private function _addButton($id, $ajaxUrl, $label, $class = null)
    {
        $button = new \Magento\Framework\DataObject();
        $button->addData(
            [
                'id'            => $id,
                'button_label'  => __($label),
                'class'         => $class,
                'ajax_url'      => $ajaxUrl
            ]
        );
        $this->_buttonsCollection[] = $button;
    }
}
