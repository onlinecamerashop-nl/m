<?php
/**
 * Copyright © MgtWizards. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */

namespace MgtWizards\Bestsellers\Controller\Adminhtml\Action;

class RegenerateProducts extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'MgtWizards_Bestsellers::config_mgtwizards_bestsellers';

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $_logger;

    /**
     * @var \MgtWizards\Bestsellers\Cron\UpdateBestsellersCategory
     */
    private $_bestsellersCategoryCron;

    /**
     * Run constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \MgtWizards\Bestsellers\Cron\UpdateBestsellersCategory $bestsellersCategoryCron
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \MgtWizards\Bestsellers\Cron\UpdateBestsellersCategory $bestsellersCategoryCron,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->_bestsellersCategoryCron = $bestsellersCategoryCron;
        $this->_logger = $logger;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
        try {
            $this->_bestsellersCategoryCron->execute();
            $result = [
                [
                    'type'      => 'success',
                    'message'   => __('Bestsellers products list successfully regenerated.')
                ]
            ];
            $resultJson->setData($result);
        } catch (\Exception $e) {
            $result = [
                [
                    'type'      => 'error',
                    'message'   => $e->getMessage()
                ]
            ];
            $resultJson->setData($result);
            $this->_logger->info($e);
        }

        return $resultJson;
    }
}
