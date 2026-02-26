<?php
/**
 * Copyright © MgtWizards. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace MgtWizards\Promotions\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field;

class Buttons extends Field
{
    const BUTTON_TEMPLATE = 'MgtWizards_Promotions::system/config/button/button.phtml';

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
            'button-to-regenerate-promotions',
            $this->_urlBuilder->getUrl('category_pro/action/regeneratePromotions'),
            'Run',
            'mgtwizards-button-left'
        );

        return $this->_toHtml();
    }

    /**
     * Get buttons collection
     *
     * @return array
     */
    public function getButtons()
    {
        return $this->_buttonsCollection;
    }

    /**
     * Add button to collection
     *
     * @param string $id
     * @param string $ajaxUrl
     * @param string $label
     * @param string|null $class
     * @return void
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