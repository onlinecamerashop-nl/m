<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MgtWizards\SlideBannerHyva\Block\Adminhtml;

/**
 * Adminhtml cms pages content block
 */
use Magento\Backend\Block\Widget\Grid\Container;
 
class Slider extends Container
{
   /**
     * Constructor
     *
     * @return void
     */
   protected function _construct()
    {
        $this->_controller = 'adminhtml_slider';
        $this->_blockGroup = 'MgtWizards_SlideBannerHyva';
        $this->_headerText = __('Manage Slider');
        $this->_addButtonLabel = __('Add Slider');
        parent::_construct();
    }
}
 
