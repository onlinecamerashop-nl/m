<?php

namespace ShopWhizzy\DigitalTheme\Model;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Spacer extends Field
{
    public function __construct(
    Context $context, array $data = []
    ) {
        parent::__construct($context, $data);
    }

    protected function _decorateRowHtml(AbstractElement $element, $html)
    {
        return '<tr id="row_' . $element->getHtmlId() . '"><td></td><td colspan="2"><hr class="whizzy-settings-spacer"></td></tr>';
    }

}
