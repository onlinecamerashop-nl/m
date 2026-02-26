<?php

namespace MgtWizards\Faqs\Block\Adminhtml\Faqs\Edit\Tab;

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
        /** @var $model \MgtWizards\Faqs\Model\Faq */
        $model = $this->_coreRegistry->registry('faqs_form_data');
        $defaultSetting = array();
        $setting = $model->getFaqsSetting();
        $data = array_merge($defaultSetting, $setting);
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('faqs_');
        $form->setFieldNameSuffix('faqs');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Setting')]
        );
        $fieldset->addField(
            'schema',
            'select',
            [
                'name' => 'faqs_setting[schema]',
                'label' => __('Json Schema'),
                'required' => false,
                'values' => [['value' => false, 'label' => __('False')], ['value' => true, 'label' => __('True')]]
            ]
        );
        $fieldset->addField(
            'lenght',
            'text',
            [
                'name' => 'faqs_setting[lenght]',
                'label' => __('Answer Lenght'),
                'required' => false,
            ]
        );
        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getTabLabel()
    {
        return __('FAQ\'s Info');
    }

    public function getTabTitle()
    {
        return __('FAQ\'s Info');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}