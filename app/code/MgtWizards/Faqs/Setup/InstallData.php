<?php

namespace MgtWizards\Faqs\Setup;

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
     * {@inheritdoc}
     */
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
        $installer->getConnection()->dropTable($installer->getTable('mgtwizards_faqs'));
        $installer->getConnection()->dropTable($installer->getTable('mgtwizards_faq'));
        $table = $installer->getConnection()
            ->newTable($installer->getTable('mgtwizards_faqs'))
            ->addColumn(
                'faqs_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Timer ID'
            )
            ->addColumn(
                'faqs_identifier',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Identifier'
            )
            ->addColumn(
                'faqs_title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Faqs Title'
            )->addColumn(
                'faqs_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                6,
                ['nullable' => false, 'default' => 1],
                'Status'
            )->addColumn(
                'faqs_setting',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Setting faqs'
            )->addColumn(
                'storeids',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['nullable' => true],
                'Stores Ids'
            )->addColumn(
                'faqs_styles',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Styles'
            )->addColumn(
                'faqs_script',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Styles'
            )->addColumn(
                'faqs_template',
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
                'faqs_identifier',
                ['faqs_identifier']
            )
            ->setComment('FAQ\'s');
        $installer->getConnection()
            ->createTable($table);

        $table = $installer->getConnection()
            ->newTable($installer->getTable('mgtwizards_faq'))
            ->addColumn(
                'faq_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Faq ID'
            )
            ->addColumn(
                'faqs_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['nullable' => false],
                'Faqs ID'
            )
            ->addColumn(
                'faq_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                6,
                ['nullable' => false, 'default' => 1],
                'Faq Type'
            )
            ->addColumn(
                'faq_question',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Faq Question'
            )->addColumn(
                'faq_answer',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                ['nullable' => true],
                'Faq Answer'
            )->addColumn(
                'faq_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                6,
                ['nullable' => false, 'default' => '1'],
                'Faq Status'
            )
            ->addColumn(
                'faq_position',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['nullable' => false],
                'Faq Position'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [],
                'Creation Time'
            )
            ->setComment('Faq Item');
        $installer->getConnection()
            ->createTable($table);

        $installer->endSetup();
    }
}
