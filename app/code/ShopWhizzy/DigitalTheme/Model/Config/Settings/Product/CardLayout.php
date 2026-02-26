<?php

namespace ShopWhizzy\DigitalTheme\Model\Config\Settings\Product;

use Magento\Framework\Option\ArrayInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\Phrase;

/**
 * Source model for Product Card Layout options with preview images.
 */
class CardLayout implements ArrayInterface
{
    /**
     * @var Repository
     */
    protected $assetRepo;

    /**
     * @param Repository $assetRepo
     */
    public function __construct(Repository $assetRepo)
    {
        $this->assetRepo = $assetRepo;
    }

    /**
     * Return options in format expected by Magento select field.
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $options = [];
        foreach ($this->toArray() as $value => $data)
        {
            $options[] = [
                'value' => $value,
                'label' => $data['label']
            ];
        }
        return $options;
    }

    /**
     * Return raw data array with labels and image URLs.
     * Used by SelectImageField to inject data-image attributes.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'item' => [
                'label' => __('Product Card - Default'),
                'image' => $this->assetRepo->getUrl('ShopWhizzy_DigitalTheme::images/product-card-01.jpg')
            ],
            'item_2' => [
                'label' => __('Product Card 2 - Hover Effect'),
                'image' => $this->assetRepo->getUrl('ShopWhizzy_DigitalTheme::images/product-card-02.jpg')
            ],
            'item_3' => [
                'label' => __('Product Card 3 - Compact'),
                'image' => $this->assetRepo->getUrl('ShopWhizzy_DigitalTheme::images/product-card-03.jpg')
            ],
            'item_9' => [
                'label' => __('Product Card 9 - Custom'),
                'image' => $this->assetRepo->getUrl('ShopWhizzy_DigitalTheme::images/product-card-09.jpg')
            ],
        ];
    }
}