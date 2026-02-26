<?php

namespace MgtWizards\SlideBannerHyva\Block\Adminhtml\Slide\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;

class Info extends Generic implements TabInterface
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    protected $_newsStatus;
    protected $_objectManager;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Config $wysiwygConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Config $wysiwygConfig,
    \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_objectManager = $objectManager;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form fields
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        /** @var $model \MgtWizards\SlideBannerHyva\Model\Slide */
        $model = $this->_coreRegistry->registry('slide_form_data');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('slide_');
        $form->setFieldNameSuffix('slide');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General')]
        );

        $fieldset->addField(
            'slider_id',
            'select',
            [
                'name' => 'slider_id',
                'label' => __('Slider'),
                'required' => false,
        'values' => $this->_getSliderOptions()
            ]
        );
        $fieldset->addField(
            'slide_status',
            'select',
            [
                'name' => 'slide_status',
                'label' => __('Status'),
                'required' => false,
                'values' => [['value' => 1, 'label' => __('Enable')], ['value' => 2, 'label' => __('Disable')]]
            ]
        );
        $fieldset->addField(
            'slide_position',
            'text',
            [
                'name' => 'slide_position',
                'label' => __('Sort Order'),
                'required' => false
            ]
        );
        $fieldset->addField(
            'slide_image',
            'image',
            [
                'name' => 'slide_image',
                'label' => __('Image'),
                'required' => false
            ]
        );
        $fieldset->addField(
            'slide_image_mob',
            'image',
            [
                'name' => 'slide_image_mob',
                'label' => __('Image XS'),
                'required' => false
            ]
        );
        $fieldset->addField(
            'slide_link',
            'text',
            [
                'name' => 'slide_link',
                'label' => __('Link Banner'),
                'required' => false
            ]
        );

        $fieldset->addField(
            'slide_main_color',
            'text',
            [
                'name' => 'slide_main_color',
                'label' => __('Main Color'),
                'title' => __('Main Color'),
                'class' => 'input-color',
                'after_element_html' => $this->getColorPickerHtml(),
            ]
        );

        $fieldset->addField(
            'slide_alt',
            'text',
            [
                'name' => 'slide_alt',
                'label' => __('Alt'),
                'required' => false
            ]
        );

        //v1
        /*$fieldset->addField(
            'slide_elements_position',
            'select',
            [
                'name' => 'slide_elements_position',
                'label' => __('Elements Position'),
                'required' => false,
                'values' => [
                    ['value' => 'absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full lg:w-1/2 text-center p-4 lg:p-0', 'label' => __('Center')],
                    ['value' => 'absolute top-12 lg:top-20 left-0 lg:left-20 w-full lg:w-1/2 text-center lg:text-left p-4 lg:p-0', 'label' => __('Top Left')],
                    ['value' => 'absolute top-12 lg:top-20 left-1/2 -translate-x-1/2 w-full lg:w-1/2 text-center p-4 lg:p-0', 'label' => __('Top Center')],
                    ['value' => 'absolute top-12 lg:top-20 right-0 lg:right-20 w-full lg:w-1/2 text-center lg:text-right p-4 lg:p-0', 'label' => __('Top Right')],
                    ['value' => 'absolute right-0 lg:right-20 top-1/2 -translate-y-1/2 w-full lg:w-1/2 text-center lg:text-right p-4 lg:p-0', 'label' => __('Middle Right')],
                    ['value' => 'absolute bottom-12 lg:bottom-20 right-0 lg:right-20 w-full lg:w-1/2 text-center lg:text-right p-4 lg:p-0', 'label' => __('Bottom Right')],
                    ['value' => 'absolute bottom-12 lg:bottom-20 left-1/2 -translate-x-1/2 w-full lg:w-1/2 text-center p-4 lg:p-0', 'label' => __('Bottom Center')],
                    ['value' => 'absolute bottom-12 lg:bottom-20 left-0 lg:left-20 w-full lg:w-1/2 text-center lg:text-left p-4 lg:p-0', 'label' => __('Bottom Left')],
                    ['value' => 'absolute left-0 lg:left-20 top-1/2 -translate-y-1/2 w-full lg:w-1/2 text-center lg:text-left p-4 lg:p-0', 'label' => __('Middle Left')]
                ]
            ]
        );*/
        $fieldset->addField(
            'slide_elements_position',
            'select',
            [
                'name' => 'slide_elements_position',
                'label' => __('Elements Position'),
                'required' => false,
                'values' => [
                    ['value' => 'center-center', 'label' => __('Center')],
                    ['value' => 'top-left', 'label' => __('Top Left')],
                    ['value' => 'top-center', 'label' => __('Top Center')],
                    ['value' => 'top-right', 'label' => __('Top Right')],
                    ['value' => 'middle-right', 'label' => __('Middle Right')],
                    ['value' => 'bottom-right', 'label' => __('Bottom Right')],
                    ['value' => 'bottom-center', 'label' => __('Bottom Center')],
                    ['value' => 'bottom-left', 'label' => __('Bottom Left')],
                    ['value' => 'middle-left', 'label' => __('Middle Left')]
                ]
            ]
        );
        $fieldset->addField(
            'slide_headline',
            'text',
            [
                'name' => 'slide_headline',
                'label' => __('Headline'),
                'required' => false
            ]
        );
        $fieldset->addField(
            'slide_heading',
            'text',
            [
                'name' => 'slide_heading',
                'label' => __('Heading'),
                'required' => false
            ]
        );
        $fieldset->addField(
            'slide_button_label',
            'text',
            [
                'name' => 'slide_button_label',
                'label' => __('Button Label'),
                'required' => false
            ]
        );
        $fieldset->addField(
            'slide_button_url',
            'text',
            [
                'name' => 'slide_button_url',
                'label' => __('Button Url'),
                'required' => false
            ]
        );
        $wysiwygConfig = $this->_wysiwygConfig->getConfig();
        $fieldset->addField(
            'slide_text',
            'editor',
            [
                'name' => 'slide_text',
                'label' => __('Banner Text'),
                'required' => false
            ]
        );
        $fieldset->addField(
            'slide_shadow',
            'select',
            [
                'name' => 'slide_shadow',
                'label' => __('Shadow'),
                'required' => false,
                'values' => [['value' => 0, 'label' => __('No')], ['value' => 1, 'label' => __('Yes')]]
            ]
        );
        $fieldset->addField(
            'slide_elements_color',
            'select',
            [
                'name' => 'slide_elements_color',
                'label' => __('Text Color'),
                'required' => false,
                'values' => [
                    ['value' => '', 'label' => __('Inherit')],
                    ['value' => 'text-black', 'label' => __('Black')],
                    ['value' => 'text-white', 'label' => __('White')],
                    ['value' => 'text-whizzy-primary', 'label' => __('Primary Color')],
                    ['value' => 'text-whizzy-primary-darker', 'label' => __('Primary Darker Color')],
                    ['value' => 'text-gray-900', 'label' => __('Dark Gray')],
                    ['value' => 'text-gray-300', 'label' => __('Light Gray')]
                ]
            ]
        );
        $fieldset->addField(
            'slide_classes',
            'text',
            [
                'name' => 'slide_classes',
                'label' => __('Tailwind Classes'),
                'required' => false
            ]
        );

        $data = $model->getData();
        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }
    protected function _getSliderOptions()
    {
        $result = [];
        $collection = $this->_objectManager->create('MgtWizards\SlideBannerHyva\Model\Slider', [])->getCollection();
        foreach ($collection as $slider)
        {
            $result[] = array('value' => $slider->getId(), 'label' => $slider->getSliderTitle());
        }
        return $result;
    }
    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Banner Info');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Banner Info');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    public function getColorPickerHtml()
    {
        return '
        <script type="text/javascript">
            require(["jquery", "spectrum"], function($) {
                $(document).ready(function() {
                    $("#slide_slide_main_color").spectrum({
                        preferredFormat: "hex",
                        allowEmpty: true,
                        showAlpha: false,
                        showInput: true,
                        move: function (tinycolor) {
                            $(this).hide();
                            var value = "";
                            if (tinycolor && tinycolor.hasOwnProperty("_a")) {
                                if (1 > tinycolor._a) {
                                    value = tinycolor.toRgbString();
                                } else {
                                    value = tinycolor.toHexString();
                                }
                                $(this).val(value);
                            }
                        }
                    }).attr("type", "hidden");
                });
            });
        </script>
    ';
    }
}