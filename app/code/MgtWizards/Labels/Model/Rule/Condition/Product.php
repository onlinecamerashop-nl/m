<?php

/**
 * @author MgtWizards Team
 * @copyright Copyright (c) MgtWizards (https://shopwhizzy.com/)
 */

namespace MgtWizards\Labels\Model\Rule\Condition;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;

/**
 * Catalog Rule Product Condition data model.
 */
class Product extends \Magento\CatalogRule\Model\Rule\Condition\Product
{
    /**
     * @inheritDoc
     */
    public function loadAttributeOptions()
    {
        $productAttributes = $this->_productResource->loadAllAttributes()->getAttributesByCode();

        $attributes = [];
        /* @var Attribute $attribute */
        foreach ($productAttributes as $attribute) {
            if ($attribute->getUsedInProductListing()) {
                $attributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
            }
        }

        $this->_addSpecialAttributes($attributes);

        asort($attributes);
        $this->setAttributeOption($attributes);

        return $this;
    }
}
