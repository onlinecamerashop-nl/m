<?php

namespace MgtWizards\Faqs\Controller\Adminhtml\Faq;

use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magento\Backend\App\Action
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
		return $this->_authorization->isAllowed('MgtWizards_Faqs::manage_faq');
	}
	protected function _initAction()
	{
		/** @var \Magento\Backend\Model\View\Result\Page $resultPage */
		$resultPage = $this->_resultPageFactory->create();
		return $resultPage;
	}
	/**
	 * @return void
	 */
	public function execute()
	{
		if ($data = $this->getRequest()->getPostValue('faq')) {
			$model = $this->_objectManager->create('MgtWizards\Faqs\Model\Faq');
			$storeViewId = $this->getRequest()->getParam("store");

			if ($id = $this->getRequest()->getParam('faq_id')) {
				$model->load($id);
				//print_r($model->getData()); die;
			}

			$model->addData($data);

			try {
				$model->save();

				$this->messageManager->addSuccess(__('The FAQ has been saved.'));
				$this->_getSession()->setFormData(false);

				if ($this->getRequest()->getParam('back') === 'edit') {
					$this->_redirect(
						'*/*/edit',
						[
							'faq_id' => $model->getId(),
							'_current' => true,
							'current_faq_id' => $this->getRequest()->getParam('current_faq_id'),
							'saveandclose' => $this->getRequest()->getParam('saveandclose'),
						]
					);

					return;
				} elseif ($this->getRequest()->getParam('back') === "new") {
					$this->_redirect('*/*/new', array('_current' => true));
					return;
				}
				$this->_redirect('*/*/');
				return;
			} catch (\Magento\Framework\Model\Exception $e) {
				$this->messageManager->addError($e->getMessage());
			} catch (\RuntimeException $e) {
				$this->messageManager->addError($e->getMessage());
			} catch (\Exception $e) {
				$this->messageManager->addError($e->getMessage());
				$this->messageManager->addException($e, __('Something went wrong while saving the FAQ.'));
			}

			$this->_getSession()->setFormData($data);
			$this->_redirect('*/*/edit', array('faq_id' => $this->getRequest()->getParam('faq_id')));
			return;
		}
		$this->_redirect('*/*/');
	}
}