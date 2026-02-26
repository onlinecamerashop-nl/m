<?php

namespace MgtWizards\Faqs\Block\Adminhtml\Faqs\Edit\Tab;

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
        /** @var $model \MgtWizards\Faqs\Model\Faq */
        $model = $this->_coreRegistry->registry('faqs_form_data');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('faqs_');
        $form->setFieldNameSuffix('faqs');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General')]
        );

        $fieldset->addField(
            'faqs_title',
            'text',
            [
                'name'        => 'faqs_title',
                'label'    => __('Title'),
                'required'     => true,
            ]
        );
        $fieldset->addField(
            'faqs_identifier',
            'text',
            [
                'name'        => 'faqs_identifier',
                'label'    => __('Identifier'),
                'required'     => true,
            ]
        );
        $fieldset->addField(
            'faqs_status',
            'select',
            [
                'name'        => 'faqs_status',
                'label'    => __('Status'),
                'required'     => false,
                'values' => [['value' => 1, 'label' => __('Enable')], ['value' => 2, 'label' => __('Disable')]]
            ]
        );/* 
        $wysiwygConfig = $this->_wysiwygConfig->getConfig();
        $fieldset->addField(
            'faq_text',
            'editor',
            [
                'name'        => 'faq_text',
                'label'    => __('Faq Text'),
                'required'     => false,
                'config'    => $wysiwygConfig
            ]
        ); */

        $data = $model->getData();
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
        return __('Faqs Info');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Faqs Info');
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
