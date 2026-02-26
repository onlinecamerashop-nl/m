<?php
namespace MgtWizards\SlideBannerHyva\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * Upgrades data for the module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.0', '<'))
        {
            $tableName = $setup->getTable('mgtwizards_slide');

            // Add slide_main_color column to mgtwizards_slide table
            $setup->getConnection()->addColumn(
                $tableName,
                'slide_main_color',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Main Color'
                ]
            );
        }

        $setup->endSetup();
    }
}