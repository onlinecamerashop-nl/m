<?php

/**
 * @author MgtWizards Team
 * @copyright Copyright (c) MgtWizards (https://shopwhizzy.com/)
 */

namespace MgtWizards\Labels\Model;

use MgtWizards\Labels\Api\Data\LabelInterface;
use MgtWizards\Labels\Api\Data\LabelExtensionInterface;
use MgtWizards\Labels\Model\Rule\Condition\Combine;
use MgtWizards\Labels\Model\Rule\Condition\CombineFactory;
use Magento\CatalogRule\Model\Data\Condition\Converter;
use Magento\CatalogRule\Model\Rule\Action\Collection;
use Magento\CatalogRule\Model\Rule\Action\CollectionFactory as RuleCollectionFactory;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Rule\Model\AbstractModel;

/**
 * Label data model
 */
class Label extends AbstractModel implements LabelInterface, IdentityInterface
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'mgtwizards_label';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getRule() in this case
     *
     * @var string
     */
    protected $_eventObject = 'label';

    /**
     * @var CombineFactory
     */
    protected $_combineFactory;

    /**
     * @var RuleCollectionFactory
     */
    protected $_actionCollectionFactory;

    /**
     * @var Data\Condition\Converter
     */
    protected $ruleConditionConverter;

    /**
     * Rule constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param TimezoneInterface $localeDate
     * @param CombineFactory $combineFactory
     * @param RuleCollectionFactory $actionCollectionFactory
     * @param ?\Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param ?\Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     * @param ?\Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param ?\Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param ?\Magento\Framework\Serialize\Serializer\Json $serializer
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        TimezoneInterface $localeDate,
        CombineFactory $combineFactory,
        RuleCollectionFactory $actionCollectionFactory,
        ?AbstractResource $resource = null,
        ?AbstractDb $resourceCollection = null,
        array $data = [],
        ?ExtensionAttributesFactory $extensionFactory = null,
        ?AttributeValueFactory $customAttributeFactory = null,
        ?Json $serializer = null
    ) {
        $this->_combineFactory = $combineFactory;
        $this->_actionCollectionFactory = $actionCollectionFactory;

        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $localeDate,
            $resource,
            $resourceCollection,
            $data,
            $extensionFactory,
            $customAttributeFactory,
            $serializer
        );
    }

    /**
     * Init resource model and id field
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(ResourceModel\Label::class);
        $this->setIdFieldName('label_id');
    }

    /**
     * Getter for rule conditions collection
     *
     * @return Combine
     */
    public function getConditionsInstance()
    {
        return $this->_combineFactory->create();
    }

    /**
     * Getter for rule actions collection
     *
     * @return Collection
     */
    public function getActionsInstance()
    {
        return $this->_actionCollectionFactory->create();
    }

    /**
     * Getter for conditions field set ID
     *
     * @param string $formName
     * @return string
     */
    public function getConditionsFieldSetId($formName = '')
    {
        return $formName . 'rule_conditions_fieldset_' . $this->getId();
    }

    /**
     * @inheritdoc
     */
    public function getLabelId()
    {
        return $this->getData(self::LABEL_ID);
    }

    /**
     * @inheritdoc
     */
    public function setLabelId($labelId)
    {
        return $this->setData(self::LABEL_ID, $labelId);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * @inheritdoc
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * @inheritdoc
     */
    public function getIsActive()
    {
        return $this->getData(self::IS_ACTIVE);
    }

    /**
     * @inheritdoc
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * @inheritdoc
     */
    public function getRuleCondition()
    {
        return $this->getRuleConditionConverter()->arrayToDataModel($this->getConditions()->asArray());
    }

    /**
     * @inheritdoc
     */
    public function setRuleCondition($condition)
    {
        $this->getConditions()
            ->setConditions([])
            ->loadArray($this->getRuleConditionConverter()->dataModelToArray($condition));
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getStopRulesProcessing()
    {
        return $this->getData(self::STOP_RULES_PROCESSING);
    }

    /**
     * @inheritdoc
     */
    public function setStopRulesProcessing($isStopProcessing)
    {
        return $this->setData(self::STOP_RULES_PROCESSING, $isStopProcessing);
    }

    /**
     * @inheritdoc
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * @inheritdoc
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return $this->getData(self::LABEL);
    }

    /**
     * @inheritdoc
     */
    public function setLabel($amount)
    {
        return $this->setData(self::LABEL, $amount);
    }

    /**
     * @inheritdoc
     */
    public function getCssClass()
    {
        return $this->getData(self::CSS_CLASS);
    }

    /**
     * @inheritdoc
     */
    public function setCssClass($class)
    {
        return $this->setData(self::CSS_CLASS, $class);
    }

    /**
     * @inheritdoc
     */
    public function getLabelImage()
    {
        return $this->getData(self::LABEL_IMAGE);
    }

    /**
     * @inheritdoc
     */
    public function setLabelImage($image)
    {
        return $this->setData(self::LABEL_IMAGE, $image);
    }

    /**
     * @inheritdoc
     */
    public function setColor($color)
    {
        return $this->setData(self::COLOR);
    }

    /**
     * @inheritdoc
     */
    public function getColor()
    {
        return $this->getData(self::COLOR);
    }

    /**
     * @inheritdoc
     */
    public function setMinimumAmount($minAmt)
    {
        return $this->setData(self::MINIMUM_AMOUNT, $minAmt);
    }

    /**
     * @inheritdoc
     */
    public function getMinimumAmount()
    {
        return $this->getData(self::MINIMUM_AMOUNT);
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(LabelExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Getter for the rule condition converter
     *
     * @return Data\Condition\Converter
     * @deprecated 100.1.0
     */
    private function getRuleConditionConverter()
    {
        if (null === $this->ruleConditionConverter)
        {
            $this->ruleConditionConverter = ObjectManager::getInstance()
                ->get(Converter::class);
        }
        return $this->ruleConditionConverter;
    }

    /**
     * @inheritDoc
     */
    public function getIdentities()
    {
        return ['price'];
    }
}