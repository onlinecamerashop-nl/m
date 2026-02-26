<?php
namespace ShopWhizzy\DigitalTheme\Block\Adminhtml\System\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Html\Select;

class MenuItemsAfter extends AbstractFieldArray
{
    protected $_iconRenderer;
    protected $_widthRenderer;

    /**
     * Get icon renderer
     *
     * @return \ShopWhizzy\DigitalTheme\Block\Adminhtml\System\Form\Field\IconRenderer
     */
    protected function _getIconRenderer()
    {
        if (!$this->_iconRenderer)
        {
            $this->_iconRenderer = $this->getLayout()->createBlock(
                \ShopWhizzy\DigitalTheme\Block\Adminhtml\System\Form\Field\IconRenderer::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->_iconRenderer;
    }

    /**
     * Get width renderer
     *
     * @return Select
     */
    protected function _getWidthRenderer()
    {
        if (!$this->_widthRenderer)
        {
            $this->_widthRenderer = $this->getLayout()->createBlock(
                \ShopWhizzy\DigitalTheme\Block\Adminhtml\System\Form\Field\WidthRenderer::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->_widthRenderer;
    }

    /**
     * Prepare to render
     *
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn('title', [
            'label' => __('Title'),
            'class' => 'required-entry',
        ]);
        $this->addColumn('url', [
            'label' => __('Url'),
            'class' => 'required-entry',
        ]);
        $this->addColumn('icon', [
            'label' => __('Icon'),
            'renderer' => $this->_getIconRenderer(),
        ]);
        $this->addColumn('block', [
            'label' => __('Block'),
            'class' => '',
        ]);
        $this->addColumn('width', [
            'label' => __('Width'),
            'renderer' => $this->_getWidthRenderer(),
        ]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * Prepare existing row data object
     *
     * @param \Magento\Framework\DataObject $row
     * @return void
     */
    protected function _prepareArrayRow(DataObject $row)
    {
        $options = [];
        $icon = $row->getIcon();
        if ($icon)
        {
            $options['option_' . $this->_getIconRenderer()->calcOptionHash($icon)] = 'selected="selected"';
        }
        $width = $row->getWidth();
        if ($width)
        {
            $options['option_' . $this->_getWidthRenderer()->calcOptionHash($width)] = 'selected="selected"';
        }
        $row->setData('option_extra_attrs', $options);
    }
}