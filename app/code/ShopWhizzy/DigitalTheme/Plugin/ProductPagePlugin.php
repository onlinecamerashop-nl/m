<?php
/**
 * ShopWhizzy DigitalTheme - Plugin to modify Hyva ProductPage ViewModel
 * Copyright © ShopWhizzy 2025. All rights reserved.
 */

declare(strict_types=1);

namespace ShopWhizzy\DigitalTheme\Plugin;

use Hyva\Theme\ViewModel\ProductPage;
use Magento\Catalog\Model\Product;

class ProductPagePlugin
{
    /**
     * Plugin to intercept getShortDescriptionForProduct and replace <br> tags with spaces before processing
     *
     * @param ProductPage $subject
     * @param Product $product
     * @param bool $excerpt
     * @param bool $stripTags
     * @return array
     */
    public function beforeGetShortDescriptionForProduct(
        ProductPage $subject,
        Product $product,
        bool $excerpt = true,
        bool $stripTags = true
    ): array {
        // Get the short description or fall back to description
        $shortDescription = $product->getShortDescription();
        if ($shortDescription)
        {
            // Replace <br>, <br/>, and <br /> with a space
            $shortDescription = str_replace(['<br>', '<br/>', '<br />'], ' ', $shortDescription);
            $product->setShortDescription($shortDescription);
        }
        elseif ($description = $product->getDescription())
        {
            // Replace <br>, <br/>, and <br /> with a space in the description
            $description = str_replace(['<br>', '<br/>', '<br />'], ' ', $description);
            $product->setDescription($description);
        }

        // Return the modified arguments
        return [$product, $excerpt, $stripTags];
    }
}