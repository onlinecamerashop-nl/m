<?php

/**
 * @author MgtWizards Team
 * @copyright Copyright (c) MgtWizards (https://shopwhizzy.com/)
 */

namespace MgtWizards\Labels\Model\ResourceModel;

class Label extends \Magento\Rule\Model\ResourceModel\AbstractResource
{
    /**
     * Initialize main table and table id field
     *
     * @return void
     * @codeCoverageIgnore
     */
    protected function _construct()
    {
        $this->_init('mgtwizards_label', 'label_id');
    }

    /**
     * Is object new
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     */
    protected function isObjectNotNew(\Magento\Framework\Model\AbstractModel $object)
    {
        // @todo Fix that from previous comment
        return $object->getId() > 0 && (!$this->_useIsObjectNew || !$object->isObjectNew());
    }
}
