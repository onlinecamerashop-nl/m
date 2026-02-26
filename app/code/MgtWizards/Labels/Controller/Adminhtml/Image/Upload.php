<?php

/**
 * @author MgtWizards Team
 * @copyright Copyright (c) MgtWizards (https://shopwhizzy.com/)
 */

namespace MgtWizards\Labels\Controller\Adminhtml\Image;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;

class Upload extends \Magento\Backend\App\Action
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Image uploader
     *
     * @var \Magento\Catalog\Model\ImageUploader
     */
    protected $imageUploader;

    /**
     * Upload constructor.
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Catalog\Model\ImageUploader $imageUploader
     * @param Action\Context $context
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Model\ImageUploader $imageUploader,
        Action\Context $context
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->imageUploader = $imageUploader;
    }

    /**
     * Upload image to tmp folder
     *
     * @return ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $imageId = $this->getRequest()->getParam('param_name', 'image');
        try {
            $result = $this->imageUploader->saveFileToTmpDir($imageId);
        } catch (\Exception $e) {
            $this->logger->error('Label image upload error - ' . $e->getMessage());
            $this->logger->error($e);
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
