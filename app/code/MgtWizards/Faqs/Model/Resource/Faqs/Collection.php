<?php

namespace MgtWizards\Faqs\Model\Resource\Faqs;

use Magento\Framework\DB\Select;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'faqs_id';
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            'MgtWizards\Faqs\Model\Faqs',
            'MgtWizards\Faqs\Model\Resource\Faqs'
        );
    }
}
