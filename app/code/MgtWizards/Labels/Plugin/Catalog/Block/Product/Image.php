<?php

/**
 * @author MgtWizards Team
 * @copyright Copyright (c) MgtWizards (https://shopwhizzy.com/)
 */

namespace MgtWizards\Labels\Plugin\Catalog\Block\Product;

use MgtWizards\Labels\Block\Product\Label;
use MgtWizards\Labels\Model\LabelRepository;
use Magento\Catalog\Model\Product;

/**
 * Plugin for catalog product image.
 *
 * @see \Magento\Catalog\Block\Product\Image
 */
class Image
{
    /**
     * @var LabelRepository
     */
    private $labelRepository;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    /**
     * @var \Magento\Framework\View\Element\BlockInterface
     */
    protected $renderer;

    /**
     * Image constructor.
     * @param LabelRepository $labelRepository
     * @param \Magento\Framework\View\LayoutInterface $layout
     */
    public function __construct(
        LabelRepository $labelRepository,
        \Magento\Framework\View\LayoutInterface $layout
    ) {
        $this->labelRepository = $labelRepository;
        $this->layout = $layout;
    }

    /**
     * Add label to product.
     *
     * @param \Magento\Catalog\Block\Product\Image $imageBlock
     * @param string $result
     * @return string
     */
    public function afterToHtml(
        \Magento\Catalog\Block\Product\Image $imageBlock,
        string $result
    ) {
        /** @var Product $product */
        $product = $imageBlock->getData('product');
        if (!$product) {
            return $result;
        }

        /** @var Label $block */
        $block = $this->getRenderer();
        $block->setProduct($product);

        $result .= $block->toHtml();

        $block->setProduct(null);

        return $result;
    }

    /**
     * Get renderer
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     */
    protected function getRenderer()
    {
        if (null === $this->renderer) {
            $this->renderer = $this->layout->getBlockSingleton(Label::class);
        }

        return $this->renderer;
    }

    /**
     * Set renderer
     *
     * @param \Magento\Framework\View\Element\BlockInterface $value
     * @return $this
     */
    public function setRenderer(\Magento\Framework\View\Element\BlockInterface $value)
    {
        $this->renderer = $value;
        return $this;
    }
}
