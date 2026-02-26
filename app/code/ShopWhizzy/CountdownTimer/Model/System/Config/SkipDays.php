<?php
/**
 * ShopWhizzy Countdown Timer Module
 *
 * This file is part of the ShopWhizzy Countdown Timer module.
 * It handles the storage of skip days configuration as JSON.
 *
 * @package   ShopWhizzy_CountdownTimer
 * @license   Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace ShopWhizzy\CountdownTimer\Model\System\Config;

use Magento\Framework\App\Config\Value;

class SkipDays extends Value
{
    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Filter out empty rows and encode the value as JSON before saving
     *
     * @return $this
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        $filteredValue = [];

        if (is_array($value))
        {
            // Remove placeholder '__empty' row
            unset($value['__empty']);

            // Filter out rows where both Name and Date are empty or whitespace
            foreach ($value as $key => $row)
            {
                $name = trim($row['Name'] ?? '');
                $date = trim($row['Date'] ?? '');

                if (!empty($name) && !empty($date))
                {
                    $filteredValue[$key] = [
                        'Name' => $name,
                        'Date' => $date
                    ];
                }
            }
        }

        // Encode as JSON, or use empty array if no valid rows
        $this->setValue(json_encode($filteredValue ?: []));

        return parent::beforeSave();
    }

    /**
     * Decode JSON value after loading
     *
     * @return $this
     */
    public function afterLoad()
    {
        $value = $this->getValue();
        if (is_string($value) && !empty($value))
        {
            try
            {
                $decodedValue = json_decode($value, true);
                $this->setValue(is_array($decodedValue) ? $decodedValue : []);
            }
            catch (\Exception $e)
            {
                $this->setValue([]);
            }
        }
        else
        {
            $this->setValue([]);
        }

        return parent::afterLoad();
    }
}