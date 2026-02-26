<?php

/**
 * @author MgtWizards Team
 * @copyright Copyright (c) MgtWizards (https://shopwhizzy.com/)
 */

namespace MgtWizards\Labels\Controller\Adminhtml\Index;

use MgtWizards\Labels\Model\LabelRepository;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Registry;

class Edit extends \Magento\Backend\App\Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'MgtWizards_Labels::labels';

    /**
     * @var LabelRepository
     */
    protected $labelRepository;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $registry;

    /**
     * Edit constructor.
     * @param LabelRepository $labelRepository
     * @param Registry $registry
     * @param Context $context
     */
    public function __construct(
        LabelRepository $labelRepository,
        Registry $registry,
        Context $context
    ) {
        $this->labelRepository = $labelRepository;
        $this->registry = $registry;
        parent::__construct($context);
    }

    /**
     * Edit label
     *
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');

        if ($id) {
            try {
                $model = $this->labelRepository->get($id);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage(__('This label no longer exists.'));
                $this->_redirect('mgtwizards_labels/*');
                return;
            }
        } else {
            /** @var \MgtWizards\Labels\Model\Label $model */
            $model = $this->_objectManager->create(\MgtWizards\Labels\Model\Label::class);
        }

        // set entered data if was error when we do save
        $data = $this->_objectManager->get(\Magento\Backend\Model\Session::class)->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $model->getConditions()->setFormName('mgtwizards_labels_form');
        $model->getConditions()->setJsFormObject(
            $model->getConditionsFieldSetId($model->getConditions()->getFormName())
        );

        $this->registry->register('current_label', $model);

        $this->_view->loadLayout();
        $this->_setActiveMenu(
            'Magento_CatalogRule::promo_catalog'
        )->_addBreadcrumb(
            __('Promotions'),
            __('Promotions')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Product Label'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getId() ? $model->getName() : __('New Label')
        );

        $breadcrumb = $id ? __('Edit Label') : __('New Label');
        $this->_addBreadcrumb($breadcrumb, $breadcrumb);
        $this->_view->renderLayout();
    }
}
