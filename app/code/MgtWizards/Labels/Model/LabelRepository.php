<?php

/**
 * @author MgtWizards Team
 * @copyright Copyright (c) MgtWizards (https://shopwhizzy.com/)
 */

namespace MgtWizards\Labels\Model;

use MgtWizards\Labels\Api\Data\LabelInterface;
use MgtWizards\Labels\Api\LabelRepositoryInterface;
use MgtWizards\Labels\Model\ResourceModel\Label\CollectionFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class LabelRepository implements LabelRepositoryInterface
{
    /**
     * @var \MgtWizards\Labels\Model\LabelFactory
     */
    protected $labelFactory;

    /**
     * @var ResourceModel\Label;
     */
    protected $labelResource;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \MgtWizards\Labels\Model\ResourceModel\Label\Collection
     */
    protected $ruleCollection;

    /**
     * @param LabelFactory $labelFactory
     * @param ResourceModel\Label $labelResource
     * @param PriceCurrencyInterface $priceCurrency
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        LabelFactory $labelFactory,
        ResourceModel\Label $labelResource,
        PriceCurrencyInterface $priceCurrency,
        CollectionFactory $collectionFactory
    ) {
        $this->labelFactory = $labelFactory;
        $this->labelResource = $labelResource;
        $this->priceCurrency = $priceCurrency;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function get($id)
    {
        /** @var Label $label */
        $label = $this->labelFactory->create();
        $this->labelResource->load($label, $id);

        if (!$label->getId())
        {
            throw new NoSuchEntityException(__('Label not found.'));
        }

        return $label;
    }

    /**
     * @inheritdoc
     */
    public function save(LabelInterface $label)
    {
        $this->labelResource->save($label);
    }

    /**
     * @inheritdoc
     */
    public function delete(LabelInterface $label)
    {
        $this->labelResource->delete($label);
    }

    /**
     * Reset rules collection
     *
     * @return void
     */
    public function reset()
    {
        $this->ruleCollection = null;
    }

    /**
     * Get rules collection
     *
     * @return ResourceModel\Label\Collection|Label[]
     */
    public function getRules()
    {
        if (null === $this->ruleCollection)
        {
            // load all label rules, match every
            /** @var \MgtWizards\Labels\Model\ResourceModel\Label\Collection $collection */
            $this->ruleCollection = $this->collectionFactory->create();
            $this->ruleCollection->addFieldToFilter('is_active', 1);
            $this->ruleCollection->addOrder('sort_order', 'ASC');
        }
        return $this->ruleCollection;
    }

    /**
     * Get labels
     *
     * @param Product|ProductInterface $product
     * @return LabelItem[]
     */
    public function getLabels(ProductInterface $product)
    {
        $labels = [];

        /** @var \MgtWizards\Labels\Model\Label $rule */
        foreach ($this->getRules() as $rule)
        {
            if ($rule->validate($product))
            {
                // TODO: extensible
                $title = $rule->getLabel();

                $regularPrice = $product->getPriceInfo()->getPrice('regular_price')->getAmount()->getValue();
                $finalPrice = $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();

                $placeholders = [
                    '{{regular_price}}' => $this->priceCurrency->format($regularPrice),
                    '{{final_price}}' => $this->priceCurrency->format($finalPrice),
                ];

                if ($regularPrice > 0 && $finalPrice > 0)
                {
                    $placeholders['{{discount_amount}}'] = $this->priceCurrency->format($finalPrice - $regularPrice);
                    $placeholders['{{discount_percent}}'] = round(100 * ($regularPrice - $finalPrice) / $regularPrice);
                }
                $showLabel = true;
                if (
                strpos($title, '{{discount_percent}}') !== false ||
                strpos($title, '{{discount_amount}}') !== false
                )
                {
                    $minAmount = $rule->getMinimumAmount();
                    $amount = $placeholders['{{discount_percent}}'];
                    if (strpos($title, '{{discount_amount}}') !== false)
                    {
                        $amount = $placeholders['{{discount_amount}}'];
                    }
                    $showLabel = $amount >= $minAmount;
                }

                $title = str_replace(array_keys($placeholders), array_values($placeholders), $title);

                $labelItem = new LabelItem();
                $labelItem->title = $title;
                $labelItem->cssClass = $rule->getCssClass();
                $labelItem->labelImage = $rule->getLabelImage();
                $labelItem->rule = $rule;
                $labelItem->color = $rule->getColor();
                $labelItem->showLabel = $showLabel;
                $labelItem->iconName = $rule->getIconName();

                $labels[] = $labelItem;
                if ($rule->getStopRulesProcessing())
                {
                    break;
                }
            }
        }

        return $labels;
    }
}
