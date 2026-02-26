<?php

namespace ShopWhizzy\DigitalTheme\Model\Config\Settings\Header;

use Magento\Framework\Option\ArrayInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\Phrase;

/**
 * Source model for Header Layout options with preview images.
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
            'header_1' => [
                'label' => __('Header 1 - Full Width'),
                'image' => $this->assetRepo->getUrl('ShopWhizzy_DigitalTheme::images/header-version-01.jpg')
            ],
            'header_2' => [
                'label' => __('Header 2 - Boxed'),
                'image' => $this->assetRepo->getUrl('ShopWhizzy_DigitalTheme::images/header-version-02.jpg')
            ],
            'header_3' => [
                'label' => __('Header 3 - Compact'),
                'image' => $this->assetRepo->getUrl('ShopWhizzy_DigitalTheme::images/header-version-03.jpg')
            ],
            'header_4' => [
                'label' => __('Header 4 - Compact & Full Width'),
                'image' => $this->assetRepo->getUrl('ShopWhizzy_DigitalTheme::images/header-version-04.jpg')
            ],
            'header_9' => [
                'label' => __('Header 9 - Custom'),
                'image' => $this->assetRepo->getUrl('ShopWhizzy_DigitalTheme::images/header-version-09.jpg')
            ],
        ];
    }
}