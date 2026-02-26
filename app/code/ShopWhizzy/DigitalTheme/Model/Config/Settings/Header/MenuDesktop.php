<?php

namespace ShopWhizzy\DigitalTheme\Model\Config\Settings\Header;

use Magento\Framework\Option\ArrayInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\Phrase;

/**
 * Source model for Desktop Menu Layout options with preview images.
 */
class MenuDesktop implements ArrayInterface
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
            'desktop_1' => [
                'label' => __('Desktop Menu 1 - Mega Menu'),
                'image' => $this->assetRepo->getUrl('ShopWhizzy_DigitalTheme::images/menu-desktop-01.jpg')
            ],
            'desktop_2' => [
                'label' => __('Desktop Menu 2 - Drill Menu'),
                'image' => $this->assetRepo->getUrl('ShopWhizzy_DigitalTheme::images/menu-desktop-02.jpg')
            ],
        ];
    }
}