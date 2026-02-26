<?php

namespace ShopWhizzy\DigitalTheme\Model\Config\Settings\Product;

use Magento\Framework\Option\ArrayInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\Phrase;

/**
 * Source model for Product Page Gallery Layout options with preview images.
 */
class GalleryLayout implements ArrayInterface
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
            'gallery_1' => [
                'label' => __('Gallery 1 - Continuous Scroll'),
                'image' => $this->assetRepo->getUrl('ShopWhizzy_DigitalTheme::images/gallery-version-01.jpg')
            ],
            'gallery_2' => [
                'label' => __('Gallery 2 - Thumbs Below'),
                'image' => $this->assetRepo->getUrl('ShopWhizzy_DigitalTheme::images/gallery-version-02.jpg')
            ],
        ];
    }
}