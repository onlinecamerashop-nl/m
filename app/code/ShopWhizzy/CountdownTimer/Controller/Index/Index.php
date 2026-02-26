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

namespace ShopWhizzy\CountdownTimer\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class Index extends Action
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var TimezoneInterface
     */
    protected $localeDate;

    /**
     * @param Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param JsonFactory $resultJsonFactory
     * @param TimezoneInterface $localeDate // Added
     */
    public function __construct(
        Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        JsonFactory $resultJsonFactory,
        TimezoneInterface $localeDate
    ) {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->localeDate = $localeDate;

        // Set the correct time zone
        date_default_timezone_set($this->scopeConfig->getValue(
            'general/locale/timezone',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ));
    }

    /**
     * Check if a given date is a skip day
     *
     * @param string $date
     * @return bool
     */
    protected function isSkipDay($date)
    {
        $availableDaysRaw = $this->scopeConfig->getValue(
            'countdowntimer/wiz_shipping_settings/available_days',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (!empty($availableDaysRaw))
        {
            $availableDays = explode(",", $availableDaysRaw);
            $weekday = date('w', strtotime($date));
            if (!in_array($weekday, $availableDays))
            {
                return true;
            }
        }

        $skipDaysRaw = $this->scopeConfig->getValue(
            'countdowntimer/wiz_shipping_settings/skip_days',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (!empty($skipDaysRaw))
        {
            $skipDaysJson = json_decode($skipDaysRaw, true);
            if (is_array($skipDaysJson))
            {
                $skipDays = array_map(function ($ar)
                {
                    return date("Y-m-d", strtotime($ar['Date']));
                }, $skipDaysJson);
                return in_array($date, $skipDays);
            }
        }

        return false;
    }

    /**
     * Get delivery data including delivery date and cutoff time
     *
     * @return array
     */
    protected function getDeliveryData()
    {
        $currentDate = date('Y-m-d H:i:s');
        $deliveryDays = (int)$this->scopeConfig->getValue(
            'countdowntimer/wiz_shipping_settings/processing_days',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ) + (int)$this->scopeConfig->getValue(
            'countdowntimer/wiz_shipping_settings/delivery_days',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $cutoffTime = $this->scopeConfig->getValue(
            'countdowntimer/wiz_shipping_settings/cutoff_time',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        // Default values if configuration is empty or invalid
        if (empty($deliveryDays))
        {
            $deliveryDays = 3;
        }
        if (empty($cutoffTime) || !preg_match('/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/', $cutoffTime))
        {
            $cutoffTime = '16:01'; // Default to provided cutoff time
        }

        // Split and validate cutoff time
        $cutoffTimeParts = explode(':', $cutoffTime);
        $hour = (int)($cutoffTimeParts[0] ?? 16); // Cast to int, default to 16
        $minute = (int)($cutoffTimeParts[1] ?? 1); // Cast to int, default to 1

        $dateNow = new \DateTime($currentDate);
        $dispatchDate = new \DateTime($currentDate);
        $addExtraDay = false;

        if (strtotime($dateNow->format('H:i:00')) > strtotime("$hour:$minute:00"))
        {
            $dispatchDate->modify('+1 day');
            $addExtraDay = true;
        }

        do
        {
            if ($this->isSkipDay($dispatchDate->format('Y-m-d')))
            {
                $dispatchDate->modify('+1 day');
            }
            else
            {
                break;
            }
        } while (true);

        $dispatchDate->setTime($hour, $minute);
        $cutoff = $dispatchDate->format('F d, Y H:i:s');

        $deliveryDate = new \DateTime($dispatchDate->format(\DateTime::ISO8601));

        if ($deliveryDays > 1)
        {
            $numOfDaysSkipped = 0;
            for ($i = 0; $i < $deliveryDays; $i++)
            {
                $deliveryDate->modify('+1 day');
                if ($this->isSkipDay($deliveryDate->format('Y-m-d')))
                {
                    $numOfDaysSkipped++;
                }
            }
            if ($numOfDaysSkipped > 0)
            {
                $deliveryDate->modify("+$numOfDaysSkipped day");
            }
        }
        else
        {
            $deliveryDate->modify("+$deliveryDays day");
        }

        $isSkipDay = true;
        while ($isSkipDay)
        {
            if ($this->isSkipDay($deliveryDate->format('Y-m-d')))
            {
                $deliveryDate->modify('+1 day');
            }
            else
            {
                $isSkipDay = false;
            }
        }

        // Localized delivery date using Magento's TimezoneInterface
        $localizedDeliveryDate = $this->localeDate->formatDateTime(
            $deliveryDate,
            \IntlDateFormatter::FULL,   // Includes weekday, e.g. "woensdag 7 januari 2026"
            \IntlDateFormatter::NONE
        );
        // If you prefer without weekday: use \IntlDateFormatter::LONG instead of FULL

        return [
            'delivery_date' => $localizedDeliveryDate,
            'now' => date('F d, Y H:i:s'),
            'cutoff' => $cutoff
        ];
    }

    /**
     * Execute the controller and return JSON response for AJAX requests
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        if ($this->getRequest()->isAjax())
        {
            return $result->setData($this->getDeliveryData());
        }
        return $result->setData([]);
    }
}