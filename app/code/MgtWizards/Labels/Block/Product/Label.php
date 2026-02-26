<?php

/**
 * @author MgtWizards Team
 * @copyright Copyright (c) MgtWizards (https://shopwhizzy.com/)
 */

namespace MgtWizards\Labels\Block\Product;

use MgtWizards\Labels\Model\LabelItem;
use MgtWizards\Labels\Model\LabelRepository;
use Magento\Catalog\Model\Product;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Label extends Template
{
    protected $_template = 'MgtWizards_Labels::product/label.phtml';

    /**
     * @var LabelRepository
     */
    protected $labelRepository;

    /**
     * @var \MgtWizards\Labels\Model\FileInfo
     */
    protected $fileInfo;

    /**
     * Label constructor.
     * @param LabelRepository $labelRepository
     * @param \MgtWizards\Labels\Model\FileInfo $fileInfo
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        LabelRepository $labelRepository,
        \MgtWizards\Labels\Model\FileInfo $fileInfo,
        Context $context,
        array $data = []
    ) {
        $this->labelRepository = $labelRepository;
        $this->fileInfo = $fileInfo;
        parent::__construct($context, $data);
    }

    /**
     * Get labels
     *
     * @return LabelItem[]
     */
    public function getLabels()
    {
        $product = $this->getProduct();
        if (!$product) {
            return [];
        }

        return $this->labelRepository->getLabels($product);
    }

    /**
     * Get product
     *
     * @return mixed
     */
    public function getProduct()
    {
        return $this->getData('product');
    }

    /**
     * Set product for label
     *
     * @param Product|null $value
     * @return Label
     */
    public function setProduct(?Product $value)
    {
        return $this->setData('product', $value);
    }

    /**
     * Get image url for label
     *
     * @param string $image
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getImageUrl($image)
    {
        return $this->fileInfo->getImageUrl($image);
    }
}
