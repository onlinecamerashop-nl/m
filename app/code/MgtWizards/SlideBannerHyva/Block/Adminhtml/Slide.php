<?php
 
namespace MgtWizards\SlideBannerHyva\Block\Adminhtml;
 
use Magento\Backend\Block\Widget\Grid\Container;
 
class Slide extends Container
{
   /**
     * Constructor
     *
     * @return void
     */
   protected function _construct()
    {
        $this->_controller = 'adminhtml_slide';
        $this->_blockGroup = 'MgtWizards_SlideBannerHyva';
        $this->_headerText = __('Manage Banner');
        $this->_addButtonLabel = __('Add Banner');
        parent::_construct();
    }
}
 