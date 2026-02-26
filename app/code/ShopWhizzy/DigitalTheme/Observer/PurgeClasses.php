<?php
declare(strict_types=1);

namespace ShopWhizzy\DigitalTheme\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use ShopWhizzy\DigitalTheme\Helper\Data;

class PurgeClasses implements ObserverInterface
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Data
     */
    private $dataHelper;

    /**
     * ConfigObserver constructor.
     * @param Filesystem $filesystem
     * @param ScopeConfigInterface $scopeConfig
     * @param Data $dataHelper
     */
    public function __construct(
        Filesystem $filesystem,
        ScopeConfigInterface $scopeConfig,
        Data $dataHelper
    ) {
        $this->filesystem = $filesystem;
        $this->scopeConfig = $scopeConfig;
        $this->dataHelper = $dataHelper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $request = $observer->getEvent()->getRequest();
        $groups = $request->getParam('groups');

        if (!is_array($groups))
        {
            return;
        }

        // Initialize array to store color classes
        $colorClasses = [];

        // Process all groups to find color picker fields
        foreach ($groups as $groupName => $group)
        {
            if (!isset($group['fields']))
            {
                continue;
            }

            foreach ($group['fields'] as $fieldName => $field)
            {
                // Check if field is a color picker (based on system.xml frontend_class)
                if ($this->isColorPickerField($groupName, $fieldName))
                {
                    $value = $field['value'] ?? $this->scopeConfig->getValue(
                        "digitaltheme_settings/{$groupName}/{$fieldName}",
                        ScopeInterface::SCOPE_STORE
                    );

                    // Use Data helper's getConfigValue to handle RGB to HEX conversion
                    $hexColor = $this->dataHelper->getConfigValue("{$groupName}/{$fieldName}");

                    if ($hexColor && preg_match('/^#[0-9a-fA-F]{6}$/', $hexColor))
                    {
                        // Determine Tailwind class prefix based on field
                        $prefix = explode(',', $this->getTailwindPrefix($fieldName));
                        if ($prefix)
                        {
                            foreach ($prefix as $p)
                            {
                                $colorClasses[] = "{$p}-[{$hexColor}]";
                            }
                        }
                    }
                }
            }
        }

        // Update purge.phtml with color classes
        if (!empty($colorClasses))
        {
            $this->updatePurgeFile($colorClasses);
        }
    }

    /**
     * Check if field is a color picker based on system.xml configuration
     * @param string $group
     * @param string $field
     * @return bool
     */
    private function isColorPickerField(string $group, string $field): bool
    {
        $colorPickerFields = [
            'general' => ['product_image_background_custom'],
            'header' => ['menu_background_color', 'menu_text_color', 'menu_text_color_hover', 'usps_background_color', 'usps_text_color'],
            'footer' => ['footer_background_custom', 'footer_text_custom', 'footer_text_hover_custom'],
        ];

        return isset($colorPickerFields[$group]) && in_array($field, $colorPickerFields[$group]);
    }

    /**
     * Get Tailwind prefix based on field name
     * @param string $fieldName
     * @return string|null
     */
    private function getTailwindPrefix(string $fieldName): ?string
    {
        $prefixes = [
            'product_image_background_custom' => 'bg',
            'menu_background_color' => 'bg',
            'menu_text_color' => 'text',
            'menu_text_color_hover' => 'hover:text,aria-expanded:text',
            'usps_background_color' => 'bg',
            'usps_text_color' => 'text',
            'footer_background_custom' => 'bg',
            'footer_text_custom' => 'text',
            'footer_text_hover_custom' => 'hover:text,aria-expanded:text',
        ];

        return $prefixes[$fieldName] ?? null;
    }

    /**
     * Update purge.phtml with Tailwind color classes
     * @param array $colorClasses
     */
    private function updatePurgeFile(array $colorClasses): void
    {
        $uniqueColorClasses = array_unique($colorClasses);
        $rootDir = $this->filesystem->getDirectoryWrite(DirectoryList::ROOT);
        $purgeFilePath = 'app/code/ShopWhizzy/DigitalTheme/view/frontend/templates/purge.phtml';
        $rootDir->create(dirname($purgeFilePath));
        $content = "<!-- " . implode(' ', $uniqueColorClasses) . " -->";

        $rootDir->writeFile($purgeFilePath, $content);
    }
}