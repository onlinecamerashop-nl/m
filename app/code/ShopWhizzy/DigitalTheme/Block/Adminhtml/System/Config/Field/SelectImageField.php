<?php

namespace ShopWhizzy\DigitalTheme\Block\Adminhtml\System\Config\Field;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Option\ArrayInterface;

class SelectImageField extends Field
{
    protected $sourceModel;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(AbstractElement $element)
    {
        $html = parent::_getElementHtml($element);

        // Try to load source model dynamically from <source_model>
        $sourceModelClass = $element->getData('field_config/source_model');

        if (!$sourceModelClass)
        {
            return $html; // nothing to do
        }

        try
        {
            /** @var ArrayInterface $model */
            $model = $this->_layout->createBlock($sourceModelClass);
        }
        catch (\Exception $e)
        {
            // Fallback: try object manager (since source models aren't blocks)
            $model = \Magento\Framework\App\ObjectManager::getInstance()->get($sourceModelClass);
        }

        if (!$model instanceof ArrayInterface)
        {
            return $html;
        }

        $dataArray = method_exists($model, 'toArray')
            ? $model->toArray()
            : $this->convertOptionArrayToMap($model->toOptionArray());

        $imageMap = [];
        foreach ($dataArray as $value => $data)
        {
            if (is_array($data))
            {
                $imageMap[$value] = $data['image'] ?? '';
            }
        }

        if (empty($imageMap))
        {
            return $html;
        }

        $fieldId = $element->getHtmlId();

        $js = '
        <script>
        require(["jquery"], function($) {
            var fieldId = "' . $fieldId . '";
            var imageMap = ' . json_encode($imageMap) . ';

            function injectDataImage() {
                var $select = $("#" + fieldId);
                if (!$select.length) return false;

                var injected = false;
                $.each(imageMap, function(value, url) {
                    var $opt = $select.find("option[value=\\"" + value + "\\"]");
                    if ($opt.length && url) {
                        $opt.attr("data-image", url);
                        injected = true;
                    }
                });

                if (injected) {
                    console.log("[select-image] data-image injected for:", fieldId);
                    $(document).trigger("selectImageDataReady", [fieldId]);
                }
                return injected;
            }

            if (!injectDataImage()) {
                $(document).on("adminSystemConfig", function() {
                    setTimeout(injectDataImage, 100);
                });
            }
        });
        </script>';

        return $html . $js;
    }

    private function convertOptionArrayToMap(array $options)
    {
        $map = [];
        foreach ($options as $opt)
        {
            if (isset($opt['value']))
            {
                $map[$opt['value']] = $opt;
            }
        }
        return $map;
    }
}
