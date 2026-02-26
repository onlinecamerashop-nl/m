<?php
namespace MgtWizards\ReviewGeoIp\Setup\Patch\Schema;

use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class CreateReviewGeoTable implements SchemaPatchInterface
{
    private $moduleDataSetup;

    public function __construct(ModuleDataSetupInterface $moduleDataSetup)
    {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    public function apply()
    {
        $connection = $this->moduleDataSetup->getConnection();
        $tableName = $this->moduleDataSetup->getTable('mgtwizards_reviewgeo');

        if (!$connection->isTableExists($tableName))
        {
            $table = $connection->newTable($tableName)
                ->addColumn(
                    'id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Primary Key'
                )
                ->addColumn(
                    'review_id',
                    Table::TYPE_BIGINT,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Review ID'
                )
                ->addColumn(
                    'ip_address',
                    Table::TYPE_TEXT,
                    45,
                    ['nullable' => true],
                    'User IP Address'
                )
                ->addColumn(
                    'city',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'User City'
                )
                ->addColumn(
                    'country',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'User Country'
                )
                ->addColumn(
                    'country_code',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'User Country Code'
                )
                ->addForeignKey(
                    $connection->getForeignKeyName(
                        $tableName,
                        'review_id',
                        $this->moduleDataSetup->getTable('review'),
                        'review_id'
                    ),
                    'review_id',
                    $this->moduleDataSetup->getTable('review'),
                    'review_id',
                    Table::ACTION_CASCADE
                )
                ->addIndex(
                    $connection->getIndexName(
                        $tableName,
                        ['review_id'],
                        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    ['review_id'],
                    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
                )
                ->setComment('MgtWizards Review Geolocation Data')
                ->setOption('charset', 'utf8mb4')
                ->setOption('collate', 'utf8mb4_general_ci');

            $connection->createTable($table);
        }
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}