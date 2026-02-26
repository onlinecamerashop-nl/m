<?php

namespace MgtWizards\Faqs\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

class Faq extends Container
{
  /**
   * Constructor
   *
   * @return void
   */
  protected function _construct()
  {
    $this->_controller = 'adminhtml_faq';
    $this->_blockGroup = 'MgtWizards_Faqs';
    $this->_headerText = __('Manage FAQ');
    $this->_addButtonLabel = __('Add FAQ');
    parent::_construct();
  }
}