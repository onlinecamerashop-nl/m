<?php

/**
 * @author MgtWizards Team
 * @copyright Copyright (c) MgtWizards (https://shopwhizzy.com/)
 */

namespace MgtWizards\Labels\Api\Data;

interface LabelInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const LABEL_ID = 'label_id';

    const NAME = 'name';

    const DESCRIPTION = 'description';

    const IS_ACTIVE = 'is_active';

    const STOP_RULES_PROCESSING = 'stop_rules_processing';

    const SORT_ORDER = 'sort_order';

    const LABEL = 'label';

    const CSS_CLASS = 'css_class';

    const LABEL_IMAGE = 'label_image';

    const COLOR = 'color';

    const MINIMUM_AMOUNT = 'minimum_amount';

    /**#@-*/

    /**
     * Returns label id field
     *
     * @return int|null
     */
    public function getLabelId();

    /**
     * Set Label Id
     *
     * @param int $labelId
     * @return $this
     */
    public function setLabelId($labelId);

    /**
     * Returns label name
     *
     * @return string
     */
    public function getName();

    /**
     * Set label name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Returns label description
     *
     * @return string|null
     */
    public function getDescription();

    /**
     * Set label description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description);

    /**
     * Returns label activity flag
     *
     * @return int
     */
    public function getIsActive();

    /**
     * Set label is active
     *
     * @param int $isActive
     * @return $this
     */
    public function setIsActive($isActive);

    /**
     * Returns label condition
     *
     * @return \Magento\CatalogRule\Api\Data\ConditionInterface|null
     */
    public function getRuleCondition();

    /**
     * Set rule condition for label
     *
     * @param \Magento\CatalogRule\Api\Data\ConditionInterface $condition
     * @return $this
     */
    public function setRuleCondition($condition);

    /**
     * Returns stop rule processing flag
     *
     * @return int|null
     */
    public function getStopRulesProcessing();

    /**
     * Set stop rules processing
     *
     * @param int $isStopProcessing
     * @return $this
     */
    public function setStopRulesProcessing($isStopProcessing);

    /**
     * Returns label sort order
     *
     * @return int|null
     */
    public function getSortOrder();

    /**
     * Set sort order
     *
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder);

    /**
     * Returns label simple action
     *
     * @return string
     */
    public function getLabel();

    /**
     * Set label
     *
     * @param string|null $label
     * @return $this
     */
    public function setLabel($label);

    /**
     * Returns label simple action
     *
     * @return string
     */
    public function getCssClass();

    /**
     * Set css class for label
     *
     * @param string|null $class
     * @return $this
     */
    public function setCssClass($class);

    /**
     * Returns label simple action
     *
     * @return string
     */
    public function getLabelImage();

    /**
     * Set label image
     *
     * @param string|null $image
     * @return $this
     */
    public function setLabelImage($image);

    /**
     * Set color in hex value
     *
     * @param string $color
     * @return $this
     */
    public function setColor($color);

    /**
     * Get color in hex value
     *
     * @return string | null
     */
    public function getColor();

    /**
     * Set minimum amount
     *
     * @param float $minAmt
     * @return mixed
     */
    public function setMinimumAmount($minAmt);

    /**
     * Get minimum amount
     *
     * @return float
     */
    public function getMinimumAmount();

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \MgtWizards\Labels\Api\Data\LabelExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \MgtWizards\Labels\Api\Data\LabelExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\MgtWizards\Labels\Api\Data\LabelExtensionInterface $extensionAttributes);
}
