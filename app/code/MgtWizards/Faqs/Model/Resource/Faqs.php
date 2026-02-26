<?php

namespace MgtWizards\Faqs\Model\Resource;

class Faqs extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('mgtwizards_faqs', 'faqs_id');
    }
    public function load(\Magento\Framework\Model\AbstractModel $object, $value, $field = null)
    {
        if (!is_numeric($value) && $field === null) {
            $field = 'faqs_identifier';
        }
        return parent::load($object, $value, $field);
    }
}
