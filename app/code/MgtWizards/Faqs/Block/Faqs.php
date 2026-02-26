<?php

namespace MgtWizards\Faqs\Block;

class Faqs extends \Magento\Framework\View\Element\Template
{
	protected $_filterProvider;
	protected $_faqsFactory;
	protected $_bannerFactory;
	protected $_scopeConfig;
	protected $_storeManager;
	protected $_faqs;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\MgtWizards\Faqs\Model\FaqsFactory $faqsFactory,
		\MgtWizards\Faqs\Model\FaqFactory $faqFactory,
		\Magento\Cms\Model\Template\FilterProvider $filterProvider,
		array $data = []
	) {
		parent::__construct($context, $data);
		$this->_faqsFactory = $faqsFactory;
		$this->_bannerFactory = $faqFactory;
		$this->_scopeConfig = $context->getScopeConfig();
		$this->_storeManager = $context->getStoreManager();
		$this->_filterProvider = $filterProvider;
	}

	protected function _beforeToHtml()
	{
		$faqsId = $this->getFaqsId();
		if ($faqsId && !$this->getTemplate()) {
			$this->setTemplate("MgtWizards_Faqs::faqs.phtml");
		}
		return parent::_beforeToHtml();
	}

	public function getFaqsCollection()
	{
		$faqsId = $this->getFaqs()->getId();
		if (!$faqsId)
			return [];
		$collection = $this->_bannerFactory->create()->getCollection();
		$collection->addFieldToFilter('faqs_id', $faqsId);
		$collection->addFieldToFilter('faq_status', 1);
		$collection->setOrder('faq_position', 'ASC');
		$collection->setOrder('faq_id', 'ASC');
		$collection->setPageSize(9999);
		return $collection;
	}

	public function getFaqs()
	{
		if (is_null($this->_faqs)):
			$faqsId = $this->getFaqsId();
			$this->_faqs = $this->_faqsFactory->create();
			$this->_faqs->load($faqsId);
		endif;
		return $this->_faqs;
	}

	public function getContentText($html)
	{
		$html = $this->_filterProvider->getPageFilter()->filter($html);
		return $html;
	}
}