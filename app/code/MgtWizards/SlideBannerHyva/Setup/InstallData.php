<?php
namespace MgtWizards\SlideBannerHyva\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Setup\EavSetup;
/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $installer = $setup;

        $installer->startSetup();
        /**
         * Install eav entity types to the eav/entity_type table
         */
        $installer->getConnection()->dropTable($installer->getTable('mgtwizards_slider'));
        $installer->getConnection()->dropTable($installer->getTable('mgtwizards_slide'));
        $table = $installer->getConnection()
            ->newTable($installer->getTable('mgtwizards_slider'))
            ->addColumn(
                'slider_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Timer ID'
            )
            ->addColumn(
                'slider_identifier',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Identifier'
            )
            ->addColumn(
                'slider_title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Slider title'
            )->addColumn(
                'slider_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                6,
                ['nullable' => false, 'default' => 1],
                'Status'
            )->addColumn(
                'slider_setting',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Setting slider'
            )->addColumn(
                'storeids',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['nullable' => true],
                'Stores Ids'
            )->addColumn(
                'slider_styles',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Styles'
            )->addColumn(
                'slider_script',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Styles'
            )->addColumn(
                'slider_template',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Template'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [],
                'Creation Time'
            )
            ->addIndex(
                'slider_identifier',
                ['slider_identifier']
            )
            ->setComment('SlideBannerHyva');
        $installer->getConnection()
            ->createTable($table);
        $table = $installer->getConnection()
            ->newTable($installer->getTable('mgtwizards_slide'))
            ->addColumn(
                'slide_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Slide ID'
            )
            ->addColumn(
                'slider_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['nullable' => false],
                'Slider ID'
            )
            ->addColumn(
                'slide_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                6,
                ['nullable' => false, 'default' => 1],
                'Type'
            )
            ->addColumn(
                'slide_text',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Text'
            )
            ->addColumn(
                'slide_image',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'File'
            )
            ->addColumn(
                'slide_image_mob',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'File Mobile'
            )
            ->addColumn(
                'slide_link',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Link'
            )
            ->addColumn(
                'slide_alt',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Alt'
            )
            ->addColumn(
                'slide_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                6,
                ['nullable' => false, 'default' => '1'],
                'Status'
            )
            ->addColumn(
                'slide_position',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                255,
                ['nullable' => true],
                'Position'
            )
            ->addColumn(
                'slide_headline',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Headline'
            )
            ->addColumn(
                'slide_heading',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Heading'
            )
            ->addColumn(
                'slide_button_label',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Button Label'
            )
            ->addColumn(
                'slide_button_url',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Button Url'
            )
            ->addColumn(
                'slide_elements_position',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Position'
            )
            ->addColumn(
                'slide_shadow',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                6,
                ['nullable' => false, 'default' => '1'],
                'Shadow'
            )
            ->addColumn(
                'slide_elements_color',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Color'
            )
            ->addColumn(
                'slide_classes',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Classes'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [],
                'Creation Time'
            )
            ->setComment('Slide item');
        $installer->getConnection()
            ->createTable($table);

        $installer->endSetup();
    }
}