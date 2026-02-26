<?php

/**
 * @author MgtWizards Team
 * @copyright Copyright (c) MgtWizards (https://shopwhizzy.com/)
 */

namespace MgtWizards\Labels\Controller\Adminhtml\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\ResultFactory;

class NewAction extends \Magento\Backend\App\Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'MgtWizards_Labels::labels';

    /**
     * Edit/Add action
     *
     * @return Forward
     */
    public function execute()
    {
        /** @var Forward $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
        return $result->forward('edit');
    }
}
