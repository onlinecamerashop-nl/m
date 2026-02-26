<?php

namespace MgtWizards\Labels\Setup\Patch\Data;

use MgtWizards\Labels\Model\Label;
use MgtWizards\Labels\Model\LabelFactory;
use Magento\CatalogRule\Api\Data\ConditionInterfaceFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Class adds Is new label
 */
class AddIsNewLabel implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var LabelFactory
     */
    private $labelFactory;

    /**
     * @var ConditionInterfaceFactory
     */
    protected $ruleConditionFactory;

    /**
     * AddIsNewLabel constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param LabelFactory $labelFactory
     * @param ConditionInterfaceFactory $ruleConditionFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        LabelFactory $labelFactory,
        ConditionInterfaceFactory $ruleConditionFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->labelFactory = $labelFactory;
        $this->ruleConditionFactory = $ruleConditionFactory;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        $ruleCondition = $this->ruleConditionFactory->create();
        $ruleCondition->setType(\MgtWizards\Labels\Model\Rule\Condition\Combine::class);
        $ruleCondition->setValue('1');
        $ruleCondition->setAggregator('all');
        $ruleCondition->setAttribute(null);
        $ruleCondition->setOperator(null);

        $ruleSubCondition = $this->ruleConditionFactory->create();
        $ruleSubCondition->setType(\MgtWizards\Labels\Model\Rule\Condition\IsNew::class);
        $ruleSubCondition->setAttribute(false);
        $ruleSubCondition->setOperator('==');
        $ruleSubCondition->setValue('1');
        $ruleSubCondition->setIsValueParsed(false);

        $ruleCondition->setConditions(
            [$ruleSubCondition]
        );

        /** @var Label $labelSetup */
        $labelSetup = $this->labelFactory->create(['setup' => $this->moduleDataSetup]);
        $labelSetup->setName('Is New')
            ->setCssClass('')
            ->setLabel('New')
            ->setIconName('bolt')
            ->setIsActive(false)
            ->setRuleCondition($ruleCondition)
            ->setStopRulesProcessing(false);

        $labelSetup->save();
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }
}
