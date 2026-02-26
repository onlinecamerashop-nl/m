<?php

namespace MgtWizards\Faqs\Block\Adminhtml\Faqs\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

class Tabs extends WidgetTabs
{
    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('faq_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Faqs Information'));
    }

    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'faqs_info',
            [
                'label' => __('General'),
                'title' => __('General'),
                'content' => $this->getLayout()->createBlock(
                    'MgtWizards\Faqs\Block\Adminhtml\Faqs\Edit\Tab\Info'
                )->toHtml(),
                'active' => true
            ]
        );
        $this->addTab(
            'faqs_setting',
            [
                'label' => __('Settings FAQ\'s'),
                'title' => __('Settings FAQ\'s'),
                'content' => $this->getLayout()->createBlock(
                    'MgtWizards\Faqs\Block\Adminhtml\Faqs\Edit\Tab\Setting'
                )->toHtml(),
                'active' => false
            ]
        );
        $this->addTab(
            'faq_info',
            [
                'label' => __('FAQ\'s Items'),
                'title' => __('FAQ\'s Items'),
                'content' => $this->getLayout()->createBlock(
                    'MgtWizards\Faqs\Block\Adminhtml\Faqs\Edit\Tab\Items'
                )->toHtml(),
                'active' => false
            ]
        );

        return parent::_beforeToHtml();
    }
}