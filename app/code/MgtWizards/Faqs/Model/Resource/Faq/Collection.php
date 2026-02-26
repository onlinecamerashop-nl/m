<?php

namespace MgtWizards\Faqs\Model\Resource\Faq;

use Magento\Framework\DB\Select;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'faq_id';
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            'MgtWizards\Faqs\Model\Faq',
            'MgtWizards\Faqs\Model\Resource\Faq'
        );
    }
    public function joinFaqs()
    {
        $this->getSelect()->joinLeft(['faqs' => $this->getTable('mgtwizards_faqs')], "main_table.faqs_id = faqs.faqs_id", ['faqs_title']);
        return $this;
    }
}
