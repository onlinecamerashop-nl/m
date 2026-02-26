<?php

namespace MgtWizards\Base\Helper;

/**
 * Brand helper
 */
class Brand extends Data
{
    /**
     * Path of show brands flag
     */
    const XML_PATH_ENABLE = 'mgtwizards_products/brand/enable';

    /**
     * Default brand attribute code
     */
    const DEFAULT_BRAND_ATTRIBUTE = 'brand';

    /**
     * Get brand attribute code
     *
     * @return string
     */
    public function getBrandAttribute()
    {
        return static::DEFAULT_BRAND_ATTRIBUTE;
    }

    /**
     * Get brand name
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getBrandName($product)
    {
        $attributeCode = $this->getBrandAttribute();
        // ugly hack to get option values from flat table and avoid additional db queries
        $valueAttributeCode = $attributeCode . '_value';
        $manufacturer = $product->hasData($valueAttributeCode)
            ? $product->getData($valueAttributeCode)
            : $product->getAttributeText($attributeCode);
        return $manufacturer ?: '';
    }

    public function slugify($text, string $divider = '-')
    {
        $text = preg_replace('~[^\pL\d]+~u', $divider, $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, $divider);
        $text = preg_replace('~-+~', $divider, $text);
        $text = strtolower($text);
        if (empty($text)) {
            return 'n-a';
        }
        return $text;
    }

    /**
     * Get brand URL
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getBrandUrl($product)
    {
        return $this->_urlBuilder->getUrl($this->slugify(($this->getBrandName($product))));
    }

    /**
     * Is brand name will showed
     *
     * @return bool
     */
    public function isEnabled()
    {
        return (bool) $this->getConfigValue(static::XML_PATH_ENABLE);
    }
}
