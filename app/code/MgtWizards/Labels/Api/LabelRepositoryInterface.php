<?php

/**
 * @author MgtWizards Team
 * @copyright Copyright (c) MgtWizards (https://shopwhizzy.com/)
 */

namespace MgtWizards\Labels\Api;

use MgtWizards\Labels\Api\Data\LabelInterface;
use MgtWizards\Labels\Model\LabelItem;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Product label management repository
 */
interface LabelRepositoryInterface
{
    /**
     * Get label
     *
     * @param int $id
     * @return LabelInterface
     * @throws NoSuchEntityException
     */
    public function get($id);

    /**
     * Save label
     *
     * @param LabelInterface $label
     * @return void
     * @throws LocalizedException
     */
    public function save(LabelInterface $label);

    /**
     * Delete label
     *
     * @param LabelInterface $label
     * @return void
     * @throws LocalizedException
     */
    public function delete(LabelInterface $label);

    /**
     * Get list of labels
     *
     * @param ProductInterface $product
     * @return LabelItem[]
     */
    public function getLabels(ProductInterface $product);
}
