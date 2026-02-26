<?php
/**
 * @author MgtWizards Team
 * @copyright Copyright (c) MgtWizards (https://shopwhizzy.com/)
 */

namespace MgtWizards\Labels\Model\Rule\Condition;

use MgtWizards\Labels\Api\LabelAttributesInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Rule\Model\Condition\Context;

/**
 * Catalog Rule Product Condition data model
 *
 * @method string getAttribute() Returns attribute code
 */
class IsNew extends \Magento\Rule\Model\Condition\AbstractCondition implements LabelAttributesInterface
{
    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * IsNew constructor.
     * @param TimezoneInterface $timezone
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        TimezoneInterface $timezone,
        Context $context,
        array $data = []
    ) {
        $this->timezone = $timezone;
        parent::__construct($context, $data);
    }

    /**
     * Get Is new attribute Html
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function getAttributeElementHtml()
    {
        return __('Is New');
    }

    /**
     * Load operator options
     *
     * @return $this
     */
    public function loadOperatorOptions()
    {
        parent::loadOperatorOptions();
        $this->setOperatorOption(
            [
                '==' => __('is'),
            ]
        );
        return $this;
    }

    /**
     * Get type element
     *
     * @return string
     */
    public function getValueElementType()
    {
        return 'select';
    }

    /**
     * Get select options
     *
     * @return array
     */
    public function getValueSelectOptions()
    {
        return [
            ['value' => 1, 'label' => __('Yes')],
            ['value' => 0, 'label' => __('No')],
        ];
    }

    /**
     * Validate product attribute value for condition
     *
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\Model\AbstractModel $product
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $product)
    {
        $isNew = $this->isProductNew($product);
        $value = $this->getValueParsed();
        // Soft comparison on purpose
        return $isNew == $value;
    }

    /**
     * Get product is new status
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    protected function isProductNew(\Magento\Catalog\Model\Product $product)
    {
        $rule = $this->getRule();
        $newProductDays = $rule->getData('new_product_days');

        if ($newProductDays !== null && $newProductDays > 0)
        {
            // Use new_product_days logic if set
            $createdAt = $product->getCreatedAt();
            if (!$createdAt)
            {
                return false;
            }

            $currentDate = $this->timezone->date();
            $createdDate = $this->timezone->date($createdAt);
            $daysDiff = $currentDate->diff($createdDate)->days;

            return $daysDiff <= $newProductDays;
        }

        // Fallback to original news_from_date and news_to_date logic
        $isNew = true;
        $dateNow = $this->timezone->date()->format('Y-m-d H:i:s');

        $dateFrom = $product->getNewsFromDate();
        $dateTo = $product->getNewsToDate();

        if ($dateFrom || $dateTo)
        {
            if ($dateFrom && $dateFrom > $dateNow)
            {
                $isNew = false;
            }
            if ($dateTo && $dateTo < $dateNow)
            {
                $isNew = false;
            }
        }
        else
        {
            $isNew = false;
        }

        return $isNew;
    }

    /**
     * Returns attributes for Is New condition
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return ['news_to_date', 'news_from_date', 'created_at'];
    }
}