<?php

namespace ShopWhizzy\DigitalTheme\Model\Config\Settings\Category;

use Magento\Framework\Option\ArrayInterface;

class ListGrid implements ArrayInterface
{
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
            'list_grid_1' => 'XS:1 SM:2 XL:4',
            'list_grid_2' => 'XS:2 SM:2 XL:4',
            'list_grid_3' => 'XS:2 SM:3 XL:5',
        ];
    }
}
