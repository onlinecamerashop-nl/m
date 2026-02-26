<?php

namespace ShopWhizzy\DigitalTheme\Model\Config\Settings\Product;

use Magento\Framework\Option\ArrayInterface;
use Magento\Framework\View\Asset\Repository;

class Version implements ArrayInterface
{
    protected $_assetRepo;

    public function __construct(
        Repository $assetRepo
    ) {
        $this->_assetRepo = $assetRepo;
    }

    public function toOptionArray()
    {
        $optionArray = [];
        $array = $this->toArray();
        foreach ($array as $key => $value)
        {
            $optionArray[] = ['value' => $key, 'label' => $value];
        }

        return $optionArray;
    }

    public function toArray()
    {
        return [
            'v1' => $this->_assetRepo->getUrl('ShopWhizzy_DigitalTheme::images/product-version-01.png'),
        ];
    }
}
