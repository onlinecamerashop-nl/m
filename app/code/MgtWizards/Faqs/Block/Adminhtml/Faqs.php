<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MgtWizards\Faqs\Block\Adminhtml;

/**
 * Adminhtml cms pages content block
 */

use Magento\Backend\Block\Widget\Grid\Container;

class Faqs extends Container
{
  /**
   * Constructor
   *
   * @return void
   */
  protected function _construct()
  {
    $this->_controller = 'adminhtml_faqs';
    $this->_blockGroup = 'MgtWizards_Faqs';
    $this->_headerText = __('Manage FAQ\s');
    $this->_addButtonLabel = __('Add FAQ\s');
    parent::_construct();
  }
}