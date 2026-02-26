<?php
namespace ShopWhizzy\DigitalTheme\Block\Adminhtml\System\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

class SliderImages extends AbstractFieldArray
{
    /**
     * Prepare to render
     *
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn('name', [
            'label' => __('Name'),
            'class' => 'required-entry',
        ]);
        $this->addColumn('file', [
            'label' => __('File'),
            'renderer' => $this->getFileRenderer(),
        ]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * Get file renderer
     *
     * @return \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
     * @throws LocalizedException
     */
    protected function getFileRenderer()
    {
        try
        {
            $renderer = $this->getLayout()->createBlock(
                \Magento\Config\Block\System\Config\Form\Field::class,
                '',
                [
                    'data' => [
                        'is_render_to_js_template' => true,
                    ]
                ]
            );
            $renderer->setTemplate('ShopWhizzy_DigitalTheme::system/config/form/field/file.phtml');
            return $renderer;
        }
        catch (LocalizedException $e)
        {
            throw new LocalizedException(__('Failed to load file renderer: %1', $e->getMessage()));
        }
    }

    /**
     * Prepare array row
     *
     * @param DataObject $row
     * @return void
     */
    protected function _prepareArrayRow(DataObject $row)
    {
        $options = [];
        $file = $row->getData('file');
        if ($file)
        {
            $options['value'] = $file;
            $options['label'] = $file;
        }
        $row->setData('option_extra_attrs', $options);
    }
}