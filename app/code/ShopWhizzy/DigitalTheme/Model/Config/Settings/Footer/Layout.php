<?php

namespace ShopWhizzy\DigitalTheme\Model\Config\Settings\Footer;

use Magento\Framework\Option\ArrayInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\Phrase;

/**
 * Source model for Footer Layout options with preview images.
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
            'footer_1' => [
                'label' => __('Footer 1 - Compact'),
                'image' => $this->assetRepo->getUrl('ShopWhizzy_DigitalTheme::images/footer-version-01.jpg')
            ],
            'footer_2' => [
                'label' => __('Footer 2 - Stacked'),
                'image' => $this->assetRepo->getUrl('ShopWhizzy_DigitalTheme::images/footer-version-02.jpg')
            ],
            'footer_9' => [
                'label' => __('Footer 9 - Custom'),
                'image' => $this->assetRepo->getUrl('ShopWhizzy_DigitalTheme::images/footer-version-09.jpg')
            ],
        ];
    }
}