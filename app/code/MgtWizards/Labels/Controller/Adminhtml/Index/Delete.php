<?php

/**
 * @author MgtWizards Team
 * @copyright Copyright (c) MgtWizards (https://shopwhizzy.com/)
 */

namespace MgtWizards\Labels\Controller\Adminhtml\Index;

use MgtWizards\Labels\Api\LabelRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class Delete extends Action implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'MgtWizards_Labels::labels';

    /**
     * @var LabelRepositoryInterface
     */
    private $labelRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LabelRepositoryInterface $labelRepository
     * @param LoggerInterface $logger
     * @param Context $context
     */
    public function __construct(
        LabelRepositoryInterface $labelRepository,
        LoggerInterface $logger,
        Context $context
    ) {
        $this->labelRepository = $labelRepository;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Delete label
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        /** @var Redirect $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if ($id) {
            try {
                $model = $this->labelRepository->get($id);
                $this->labelRepository->delete($model);
                $this->messageManager->addSuccessMessage(__('You deleted the label.'));
                return $result->setPath('mgtwizards_labels/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t delete the label right now. Please review the log and try again.')
                );
                $this->logger->critical($e);
                return $result->setPath('mgtwizards_labels/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a label to delete.'));
        return $result->setPath('mgtwizards_labels/*/');
    }
}
