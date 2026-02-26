<?php

/**
 * @author MgtWizards Team
 * @copyright Copyright (c) MgtWizards (https://shopwhizzy.com/)
 */

namespace MgtWizards\Labels\Model\Rule\Condition;

/**
 * Catalog Rule Product Condition data model
 *
 * @method string getAttribute() Returns attribute code
 */
class DiscountPercent extends \Magento\Rule\Model\Condition\AbstractCondition
{
    /**
     * Get attribute element html
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function getAttributeElementHtml()
    {
        return __('Discount percent');
    }

    /**
     * Load operator conditions
     *
     * @return $this|\Magento\Rule\Model\Condition\AbstractCondition
     */
    public function loadOperatorOptions()
    {
        parent::loadOperatorOptions();
        $this->setOperatorOption(
            [
                //'==' => __('is'),
                //'!=' => __('is not'),
                '>' => __('greater than'),
                '<' => __('less than'),
                '>=' => __('equals or greater than'),
                '<=' => __('equals or less than'),
            ]
        );
        return $this;
    }

    /**
     * Validate product attribute value for condition
     *
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\Model\AbstractModel $product
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $product)
    {
        $regularPrice = $product->getPriceInfo()->getPrice('regular_price')->getAmount()->getValue();
        $finalPrice = $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();

        if ($regularPrice > 0 && $finalPrice > 0 && $finalPrice < $regularPrice) {
            $validatedValue = round(100 * ($regularPrice - $finalPrice) / $regularPrice);
            return $this->validateAttribute($validatedValue);
        }

        return false;
    }
}
