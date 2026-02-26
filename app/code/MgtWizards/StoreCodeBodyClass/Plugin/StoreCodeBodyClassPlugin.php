<?php

namespace MgtWizards\StoreCodeBodyClass\Plugin;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\View\Page\Config;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Registry;
use \Magento\Customer\Model\Session;


class StoreCodeBodyClassPlugin implements ObserverInterface
{
	protected $config;
	protected $storeManager;
	protected $request;
	private $registry;
	protected $customerSession;
	protected $toolbar = null;
	protected $pager = null;

	public function __construct(
	 Config $config,
	 StoreManagerInterface $storeManager,
	 Http $request,
	 Registry $registry,
	 Session $customerSession,
	 \Magento\Catalog\Block\Product\ProductList\Toolbar $toolbar,
	 \Magento\Theme\Block\Html\Pager $pager
	) {
		$this->config = $config;
		$this->storeManager = $storeManager;
		$this->request = $request;
		$this->registry = $registry;
		$this->customerSession = $customerSession;
		$this->toolbar = $toolbar;
		$this->pager = $pager;
	}

	public function execute(Observer $observer)
	{
		$store = $this->storeManager->getStore();
		$storeCode = $store->getCode();
		$websiteCode = $store->getWebsite()->getCode();

		$this->config->addBodyClass('store_code_' . $storeCode);
		$this->config->addBodyClass('website_code_' . $websiteCode);

		/*
		if ($this->request->getFullActionName() == 'catalog_product_view') {
		$product = $this->registry->registry('current_product');
		$attribute_id = $product->getAttributeSetId();
		$this->config->addBodyClass('product_attrset_'.$attribute_id);
		}
		*/

		if ($this->request->getFullActionName() == 'catalog_category_view')
		{
			$category = $this->registry->registry('current_category');
			if ($category)
			{
				if ($category->getId())
				{
					//$this->config->setPageLayout('1column');
					//$this->config->addBodyClass('category_id_' . $category->getId());
				}

				$params = $this->request->getParams();
				if (isset($params['p']))
				{
					//$this->config->setRobots('INDEX,FOLLOW');
				}
			}
		}

		if ($this->request->getFullActionName() == 'catalogsearch_result_index')
		{
			//$this->config->setPageLayout('1column');
		}

		//if($this->customerSession->isLoggedIn()) {
		//	$this->config->addBodyClass('customer_logged_in');
		//} else {
		//	$this->config->addBodyClass('customer_not_logged_in');
		//}

	}
}
