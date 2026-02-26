<?php

/**
 * @author MgtWizards Team
 * @copyright Copyright (c) MgtWizards (https://shopwhizzy.com/)
 */

namespace MgtWizards\Labels\Controller\Adminhtml\Index;

use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'MgtWizards_Labels::labels';

    /**
     * Display list of labels
     *
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('MgtWizards_Labels::labels');
        $resultPage->getConfig()->getTitle()->prepend(__('Labels'));

        return $resultPage;
    }
}
