<?php
 
namespace MgtWizards\SlideBannerHyva\Model\Resource;
 
class Slide extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('mgtwizards_slide', 'slide_id');
    }
}