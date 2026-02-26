<?php
/**
 * Copyright © MgtWizards. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */

namespace MgtWizards\Promotions\Controller\Adminhtml\Action;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use MgtWizards\Promotions\Helper\Data;
use Psr\Log\LoggerInterface;

class RegeneratePromotions extends Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'MgtWizards_Promotions::regenerate_promotions';

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $_logger;

    /**
     * @var \MgtWizards\Promotions\Helper\Data
     */
    private $_dataHelper;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \MgtWizards\Promotions\Helper\Data $dataHelper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Data $dataHelper,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->_dataHelper = $dataHelper;
        $this->_logger = $logger;
    }

    /**
     * Execute action to regenerate promotions category
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        try
        {
            $this->_dataHelper->syncPromotionsCategory();
            $result = [
                [
                    'type' => 'success',
                    'message' => __('Promotions category products successfully regenerated.')
                ]
            ];
            $resultJson->setData($result);
        }
        catch (\Exception $e)
        {
            $result = [
                [
                    'type' => 'error',
                    'message' => $e->getMessage()
                ]
            ];
            $resultJson->setData($result);
            $this->_logger->error('Error regenerating promotions category: ' . $e->getMessage());
        }

        return $resultJson;
    }
}