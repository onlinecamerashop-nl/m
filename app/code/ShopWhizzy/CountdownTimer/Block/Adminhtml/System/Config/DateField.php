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

namespace ShopWhizzy\CountdownTimer\Block\Adminhtml\System\Config;

use Magento\Framework\View\Element\Html\Date;
class DateField extends Date
{

    /**
     * Render block HTML
     *
     * @return string
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _toHtml()
    {
        $html = '<input type="text" name="' . $this->getInputName() . '" id="' . $this->getInputId() . '" ';
        $html .= 'value="<%- ' . $this->getColumnName() . ' %>" ';
        $html .= 'class="' . $this->getClass() . '" style="width: 115px;"' . $this->getExtraParams() . '/> ';
        $calendarYearsRange = $this->getYearsRange();
        $changeMonth = $this->getChangeMonth();
        $changeYear = $this->getChangeYear();
        $maxDate = $this->getMaxDate();
        $showOn = $this->getShowOn();

        $html .= '<script type="text/javascript">require(["jquery", "mage/calendar"], function($){ $("#' .
                 $this->getInputId() .
                 '").calendar({showsTime: ' .
                 ($this->getTimeFormat() ? 'true' : 'false') .
                 ',' .
                 ($this->getTimeFormat() ? 'timeFormat: "' .
                                            $this->getTimeFormat() .
                                            '",' : '') .
                 'dateFormat: "' .
                 $this->getDateFormat() .
                 '",buttonImage: "' .
                 $this->getImage() .
                 '",' .
                 ($calendarYearsRange ? 'yearRange: "' .
                                         $calendarYearsRange .
                                         '",' : '') .
                 'buttonText: "' .
                 (string)new \Magento\Framework\Phrase(
                     'Select Date'
                 ) .
                 '"' . ($maxDate ? ', maxDate: "' . $maxDate . '"' : '') .
                 ($changeMonth === null ? '' : ', changeMonth: ' . $changeMonth) .
                 ($changeYear === null ? '' : ', changeYear: ' . $changeYear) .
                 ($showOn ? ', showOn: "' . $showOn . '"' : '') .
                 '})});<\'+\'/script>';

        return $html;
    }
}