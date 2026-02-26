<?php
namespace ShopWhizzy\DigitalTheme\Plugin;

class SystemConfigFormPlugin
{
    /**
     * Add enctype to system config form
     *
     * @param \Magento\Config\Block\System\Config\Form $subject
     * @param \Magento\Framework\Data\Form $result
     * @return \Magento\Framework\Data\Form
     */
    public function afterGetForm(\Magento\Config\Block\System\Config\Form $subject, $result)
    {
        $result->setEnctype('multipart/form-data');
        return $result;
    }
}