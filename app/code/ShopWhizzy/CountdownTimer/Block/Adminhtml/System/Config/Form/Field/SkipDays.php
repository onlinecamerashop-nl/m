<?php
/**
 * ShopWhizzy Countdown Timer Module
 *
 * This file is part of the ShopWhizzy Countdown Timer module.
 * It displays a countdown timer for next day delivery options.
 *
 * @package   ShopWhizzy_CountdownTimer
 * @license   Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace ShopWhizzy\CountdownTimer\Block\Adminhtml\System\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Registry;

class SkipDays extends AbstractFieldArray
{
    protected $_coreRegistry;

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    protected function _prepareToRender()
    {

        $this->addColumn(
            'Date',
            [
                'label' => __('Date'),
                'id' => 'select_date',
                'class' => 'required-entry',
                'class' => 'daterecuring',
            ]
        );

        $this->addColumn(
            'Name',
            [
                'label' => __('Content'),
                'class' => 'required-entry',
                'style' => 'width:300px',
            ]
        );

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];
        $row->setData('option_extra_attrs', $options);
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = parent::_getElementHtml($element);

        $script = '<script type="text/javascript">
                require(["jquery", "jquery/ui", "mage/calendar"], function (jq) {
                    jq(function(){
                        function bindDatePicker() {
                            setTimeout(function() {
                                jq(".daterecuring").datepicker( { dateFormat: "mm/dd/yy" } );
                            }, 50);
                        }
                        bindDatePicker();
                        jq("button.action-add").on("click", function(e) {
                            bindDatePicker();
                        });
                    });
                });
            </script>';
        $html .= $script;
        return $html;
    }
}
