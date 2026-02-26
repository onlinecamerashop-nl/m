<?php

namespace MgtWizards\Faqs\Controller\Adminhtml\Faqs;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Result page factory
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * News model factory
     *
     * @var \Tutorial\SimpleNews\Model\NewsFactory
     */
    protected $_faqFactory;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param NewsFactory $newsFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * News access rights checking
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MgtWizards_Faqs::manage_faqs');
    }
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('MgtWizards_Faqs::manage_faqs');
        return $resultPage;
    }
    /**
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('faqs_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create('MgtWizards\Faqs\Model\Faqs');
                $model->load($id);
                $name = $model->getFaqsTitle();
                $model->delete();
                // display success message
                $this->messageManager->addSuccess(__('You deleted the Faqs %1.', $name));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['faqs_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a Faqs to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
