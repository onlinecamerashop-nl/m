<?php

namespace MgtWizards\Faqs\Block\Adminhtml\Faq\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;

class Info extends Generic implements TabInterface
{
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

    protected function _prepareForm()
    {
        /** @var $model \MgtWizards\Faqs\Model\Faq */
        $model = $this->_coreRegistry->registry('faq_form_data');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('faq_');
        $form->setFieldNameSuffix('faq');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General')]
        );

        $fieldset->addField(
            'faqs_id',
            'select',
            [
                'name' => 'faqs_id',
                'label' => __('Faqs'),
                'required' => false,
                'values' => $this->_getFaqsOptions()
            ]
        );
        $fieldset->addField(
            'faq_status',
            'select',
            [
                'name' => 'faq_status',
                'label' => __('Status'),
                'required' => false,
                'values' => [['value' => 1, 'label' => __('Enable')], ['value' => 2, 'label' => __('Disable')]]
            ]
        );
        $fieldset->addField(
            'faq_position',
            'text',
            [
                'name' => 'faq_position',
                'label' => __('Sort Order'),
                'required' => false
            ]
        );

        $fieldset->addField(
            'faq_question',
            'text',
            [
                'name' => 'faq_question',
                'label' => __('Question'),
                'required' => false
            ]
        );
        $wysiwygConfig = $this->_wysiwygConfig->getConfig();
        $fieldset->addField(
            'faq_answer',
            'editor',
            [
                'name' => 'faq_answer',
                'label' => __('Answer'),
                'required' => false,
                'config' => $wysiwygConfig
            ]
        );

        $data = $model->getData();
        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }
    protected function _getFaqsOptions()
    {
        $result = [];
        $collection = $this->_objectManager->create('MgtWizards\Faqs\Model\Faqs', [])->getCollection();
        foreach ($collection as $faqs) {
            $result[] = array('value' => $faqs->getId(), 'label' => $faqs->getFaqsTitle());
        }
        return $result;
    }

    public function getTabLabel()
    {
        return __('FAQ Info');
    }

    public function getTabTitle()
    {
        return __('FAQ Info');
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