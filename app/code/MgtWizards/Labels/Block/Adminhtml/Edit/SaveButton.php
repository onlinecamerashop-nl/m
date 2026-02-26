<?php

/**
 * @author MgtWizards Team
 * @copyright Copyright (c) MgtWizards (https://shopwhizzy.com/)
 */

namespace MgtWizards\Labels\Block\Adminhtml\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class SaveButton implements ButtonProviderInterface
{
    /**
     * Get Save Button Data
     *
     * @return array
     */
    public function getButtonData()
    {
        $data = [
            'label' => __('Save'),
            'class' => 'save primary',
            'on_click' => '',
        ];
        return $data;
    }
}
