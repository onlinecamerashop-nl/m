<?php

namespace MgtWizards\Faqs\Block\Adminhtml\Faq\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

class Tabs extends WidgetTabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('faq_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('FAQ Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab(
            'news_info',
            [
                'label' => __('General'),
                'title' => __('General'),
                'content' => $this->getLayout()->createBlock(
                    'MgtWizards\Faqs\Block\Adminhtml\Faq\Edit\Tab\Info'
                )->toHtml(),
                'active' => true
            ]
        );

        return parent::_beforeToHtml();
    }
}