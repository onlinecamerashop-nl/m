<?php

namespace ShopWhizzy\DigitalTheme\Model\Config\Settings\Category;

use Magento\Framework\Option\ArrayInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\Phrase;

/**
 * Source model for Subcategories Top Layout options with preview images.
 */
class SubcategoriesTop implements ArrayInterface
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
            'top_1' => [
                'label' => __('Top 1 - List'),
                'image' => $this->assetRepo->getUrl('ShopWhizzy_DigitalTheme::images/subcategory-top-version-01.jpg')
            ],
            'top_2' => [
                'label' => __('Top 2 - Boxes'),
                'image' => $this->assetRepo->getUrl('ShopWhizzy_DigitalTheme::images/subcategory-top-version-02.jpg')
            ],
        ];
    }
}