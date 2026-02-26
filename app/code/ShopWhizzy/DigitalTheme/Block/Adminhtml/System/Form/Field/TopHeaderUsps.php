<?php
namespace ShopWhizzy\DigitalTheme\Block\Adminhtml\System\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

class TopHeaderUsps extends AbstractFieldArray
{
    protected $_iconRenderer;

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
        $this->addColumn('icon', [
            'label' => __('Icon'),
            'renderer' => $this->_getIconRenderer(),
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
        $row->setData('option_extra_attrs', $options);
    }
}