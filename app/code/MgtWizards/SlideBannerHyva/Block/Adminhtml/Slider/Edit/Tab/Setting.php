<?php

namespace MgtWizards\SlideBannerHyva\Block\Adminhtml\Slider\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;

class Setting extends Generic implements TabInterface
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
        /** @var $model \MgtWizards\SlideBannerHyva\Model\Slider */
        $model = $this->_coreRegistry->registry('slider_form_data');
        $defaultSetting = array();
        $setting = $model->getSliderSetting();
        $data = array_merge($defaultSetting, $setting);
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('slider_');
        $form->setFieldNameSuffix('slider');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Setting')]
        );
        $fieldset->addField(
            'autoplay',
            'select',
            [
                'name' => 'slider_setting[autoplay]',
                'label' => __('Autoplay'),
                'required' => false,
                'values' => [['value' => false, 'label' => __('False')], ['value' => true, 'label' => __('True')]]
            ]
        );
        $fieldset->addField(
            'autoplaytime',
            'text',
            [
                'name' => 'slider_setting[autoplaytime]',
                'label' => __('Autoplay Time'),
                'required' => false,
            ]
        );
        $fieldset->addField(
            'progress_bar',
            'select',
            [
                'name' => 'slider_setting[progress_bar]',
                'label' => __('Progress Bar'),
                'required' => false,
                'values' => [['value' => false, 'label' => __('False')], ['value' => true, 'label' => __('True')]]
            ]
        );
        $fieldset->addField(
            'navigation',
            'select',
            [
                'name' => 'slider_setting[navigation]',
                'label' => __('Navigation'),
                'required' => false,
                'values' => [['value' => false, 'label' => __('False')], ['value' => true, 'label' => __('True')]]
            ]
        );
        $fieldset->addField(
            'pagination',
            'select',
            [
                'name' => 'slider_setting[pagination]',
                'label' => __('Pagination'),
                'required' => false,
                'values' => [['value' => false, 'label' => __('False')], ['value' => true, 'label' => __('True')]]
            ]
        );
        $fieldset->addField(
            'classes',
            'textarea',
            [
                'name' => 'slider_setting[classes]',
                'label' => __('Banner Classes'),
                'required' => false
            ]
        );

        $fieldset->addField(
            'rounded',
            'select',
            [
                'name' => 'slider_setting[rounded]',
                'label' => __('Rounded Corners'),
                'required' => false,
                'values' => [['value' => false, 'label' => __('False')], ['value' => true, 'label' => __('True')]]
            ]
        );

        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Slider Info');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Slider Info');
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
}