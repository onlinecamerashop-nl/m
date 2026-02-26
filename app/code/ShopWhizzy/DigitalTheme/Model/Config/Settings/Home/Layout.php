<?php

namespace ShopWhizzy\DigitalTheme\Model\Config\Settings\Home;

use Magento\Framework\Option\ArrayInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\Phrase;

/**
 * Source model for Home Page Layout options with preview images.
 */
class Layout implements ArrayInterface
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
            'home_1' => [
                'label' => __('Home 1 - Electronics'),
                'image' => $this->assetRepo->getUrl('ShopWhizzy_DigitalTheme::images/home-version-01.jpg')
            ],
            'home_2' => [
                'label' => __('Home 2 - Gadgets'),
                'image' => $this->assetRepo->getUrl('ShopWhizzy_DigitalTheme::images/home-version-02.jpg')
            ],
            'home_3' => [
                'label' => __('Home 3 - Tuning'),
                'image' => $this->assetRepo->getUrl('ShopWhizzy_DigitalTheme::images/home-version-03.jpg')
            ],
            'home_9' => [
                'label' => __('Home 9 - Custom'),
                'image' => $this->assetRepo->getUrl('ShopWhizzy_DigitalTheme::images/home-version-09.jpg')
            ],
        ];
    }
}