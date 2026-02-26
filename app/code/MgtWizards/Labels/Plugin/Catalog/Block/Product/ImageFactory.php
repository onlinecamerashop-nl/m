<?php

/**
 * @author MgtWizards Team
 * @copyright Copyright (c) MgtWizards (https://shopwhizzy.com/)
 */

namespace MgtWizards\Labels\Plugin\Catalog\Block\Product;

use Magento\Catalog\Block\Product\Image as ImageBlock;
use Magento\Catalog\Model\Product;

class ImageFactory
{
    /**
     * Sets product instance to image data
     *
     * @param \Magento\Catalog\Block\Product\ImageFactory $subject
     * @param ImageBlock $result
     * @param Product $product
     * @return ImageBlock
     */
    public function afterCreate(
        \Magento\Catalog\Block\Product\ImageFactory $subject,
        ImageBlock $result,
        Product $product
    ): ImageBlock {
        $result->setData('product', $product);

        return $result;
    }
}
