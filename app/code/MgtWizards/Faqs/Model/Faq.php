<?php

namespace MgtWizards\Faqs\Model;

class Faq extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Define resource model
     */
    const BASE_MEDIA_PATH = 'faqs';
    protected function _construct()
    {
        $this->_init('MgtWizards\Faqs\Model\Resource\Faq');
    }
}
