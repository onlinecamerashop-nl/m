<?php

namespace MgtWizards\Faqs\Model\Resource;

class Faq extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('mgtwizards_faq', 'faq_id');
    }
}
