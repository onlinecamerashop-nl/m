<?php

/**
 * @author MgtWizards Team
 * @copyright Copyright (c) MgtWizards (https://shopwhizzy.com/)
 */

/**
 * Catalog Rule Combine Condition data model
 */

namespace MgtWizards\Labels\Model\Rule\Condition;

use MgtWizards\Labels\Model\Rule\Condition\Product;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

class Combine extends \Magento\Rule\Model\Condition\Combine
{
    /**
     * @var ProductAttributeRepositoryInterface
     */
    private $productAttributeRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * Construct for combining
     *
     * @param ProductAttributeRepositoryInterface $productAttributeRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param array $data
     */
    public function __construct(
        ProductAttributeRepositoryInterface $productAttributeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Rule\Model\Condition\Context $context,
        array $data = []
    ) {
        $this->productAttributeRepository = $productAttributeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        parent::__construct($context, $data);
        $this->setType(Combine::class);
    }

    /**
     * Get new child select options
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $this->searchCriteriaBuilder->addFilter('used_in_product_listing', true);
        $result = $this->productAttributeRepository->getList($this->searchCriteriaBuilder->create());

        $attributes = [];
        foreach ($result->getItems() as $attribute) {
            $attributes[] = [
                'value' => Product::class . '|' . $attribute->getAttributeCode(),
                'label' => $attribute->getDefaultFrontendLabel(),
            ];
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive(
            $conditions,
            [
                [
                    'label' => __('Conditions Combination'),
                    'value' => Combine::class,
                ],
                [
                    'label' => __('Product Attribute'),
                    'value' => $attributes,
                ],
                [
                    'label' => __('Product Price'),
                    'value' => [
                        [
                            'label' => __('Discount Amount'),
                            'value' => DiscountAmount::class,
                        ],
                        [
                            'label' => __('Discount Percent'),
                            'value' => DiscountPercent::class,
                        ],
                    ],
                ],
                [
                    'label' => __('System'),
                    'value' => [
                        [
                            'label' => __('Is New'),
                            'value' => IsNew::class,
                        ],
                    ],
                ]
            ]
        );
        return $conditions;
    }

    /**
     * Collect validated attributes
     *
     * @param array $productCollection
     * @return $this
     */
    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            /** @var Product|Combine $condition */
            $condition->collectValidatedAttributes($productCollection);
        }
        return $this;
    }
}
