<?php

namespace MgtWizards\Labels\Setup\Patch\Data;

use MgtWizards\Labels\Model\Label;
use MgtWizards\Labels\Model\LabelFactory;
use Magento\Config\Console\Command\EmulatedAdminhtmlAreaProcessor;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\CatalogRule\Api\Data\ConditionInterfaceFactory;

/**
 * Class for creating Discount Label
 */
class AddDiscountLabel implements DataPatchInterface
{
    /**
     * Emulator adminhtml area for CLI command.
     *
     * @var EmulatedAdminhtmlAreaProcessor
     */
    private $emulatedAreaProcessor;

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
    private $ruleConditionFactory;

    /**
     * @param EmulatedAdminhtmlAreaProcessor $emulatedAdminhtmlAreaProcessor
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param LabelFactory $labelFactory
     * @param ConditionInterfaceFactory $ruleConditionFactory
     */
    public function __construct(
        EmulatedAdminhtmlAreaProcessor $emulatedAdminhtmlAreaProcessor,
        ModuleDataSetupInterface $moduleDataSetup,
        LabelFactory $labelFactory,
        ConditionInterfaceFactory $ruleConditionFactory
    ) {
        $this->emulatedAreaProcessor = $emulatedAdminhtmlAreaProcessor;
        $this->moduleDataSetup = $moduleDataSetup;
        $this->labelFactory = $labelFactory;
        $this->ruleConditionFactory = $ruleConditionFactory;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        $this->emulatedAreaProcessor->process(function ()
        {
            $ruleCondition = $this->ruleConditionFactory->create();
            $ruleCondition->setType(\MgtWizards\Labels\Model\Rule\Condition\Combine::class);
            $ruleCondition->setValue('1');
            $ruleCondition->setAggregator('all');
            $ruleCondition->setAttribute(null);
            $ruleCondition->setOperator(null);

            $ruleSubCondition = $this->ruleConditionFactory->create();
            $ruleSubCondition->setType(\MgtWizards\Labels\Model\Rule\Condition\DiscountAmount::class);
            $ruleSubCondition->setAttribute(false);
            $ruleSubCondition->setOperator('>');
            $ruleSubCondition->setValue('0');
            $ruleSubCondition->setIsValueParsed(false);

            $ruleCondition->setConditions(
                [$ruleSubCondition]
            );

            // set new resource model paths
            /** @var Label $labelSetup */
            $labelSetup = $this->labelFactory->create();
            $labelSetup->setName('You save X%')
                ->setCssClass('')
                ->setLabel('Sale {{discount_percent}}%')
                ->setIsActive(false)
                ->setRuleCondition($ruleCondition)
                ->setStopRulesProcessing(false);

            $labelSetup->save();
        });
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
