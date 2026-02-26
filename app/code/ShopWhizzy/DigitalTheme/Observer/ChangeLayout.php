<?php

namespace ShopWhizzy\DigitalTheme\Observer;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Page\Config;
use ShopWhizzy\DigitalTheme\Helper\Data;

class ChangeLayout implements ObserverInterface
{

    /**
     * @var Config
     */
    protected $config;
    /**
     * @var RequestInterface
     */
    private $_request;
    /**
     * @var Helper
     */
    private $_helper;
    /**
     * @var Registry
     */
    private $_registry;
    /**
     * @var PageRepositoryInterface
     */
    private $_pageRepository;

    /**
     * ChangeLayout constructor.
     * @param Config $config
     * @param RequestInterface $request
     * @param Registry $registry
     * @param PageRepositoryInterface $pageRepository
     * @param Data $helper
     */
    public function __construct(
        Config $config,
        RequestInterface $request,
        Registry $registry,
        PageRepositoryInterface $pageRepository,
        Data $helper
    ) {
        $this->config = $config;
        $this->_request = $request;
        $this->_registry = $registry;
        $this->_pageRepository = $pageRepository;
        $this->_helper = $helper;
    }

    /**
     * @param Observer $observer
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        if (!$this->_helper->getConfigValue(Data::XML_ENABLED))
        {
            return;
        }
        $variable = '';
        $actionName = $this->_request->getFullActionName();
        switch ($actionName)
        {
            case 'catalog_category_view':
                $category = $this->getCurrentCategory();
                if ($category)
                {
                    $variable = $category->getData('page_layout');
                }

                if (($category
                && !$category->getData('custom_use_parent_settings')
                && empty($variable)
                )
                || !$category
                )
                {
                    $variable = $this->getConfigValue('products_listing/category_page_layout');
                }
                break;
            case 'cms_page_view':
                $page = $this->getCurrentPage();
                if ($page)
                {
                    $variable = $page->getData('page_layout');
                }

                if (empty($variable) || 'empty' !== $variable)
                {
                    $variable = $this->getConfigValue('cms_pages/cms_page_layout');
                }
                break;
            case 'catalogsearch_result_index':
                $variable = $this->getConfigValue('products_listing/search_results_layout');
                break;
            case 'catalog_product_view':
                $product = $this->getCurrentProduct();
                if ($product)
                {
                    $variable = $product->getData('page_layout');
                }

                if (empty($variable))
                {
                    $variable = $this->getConfigValue('product/product_page_layout');
                }
                break;
            case 'blog_index_index':
            case 'blog_search_index':
            case 'blog_archive_view':
                $variable = $this->getConfigValue('blog/blog_list_page_layout');
                break;
        }

        if (!empty($variable))
        {
            $this->config->setPageLayout($variable);
        }
    }

    /**
     * @return Category
     */
    protected function getCurrentCategory()
    {
        return $this->_registry->registry('current_category') ?: $this->_registry->registry('category');
    }

    /**
     * @param string $path
     * @return mixed
     */
    protected function getConfigValue($path = '')
    {
        return $this->_helper->getConfigValue($path);
    }

    /**
     * @return PageInterface
     */
    protected function getCurrentPage()
    {
        try
        {
            $pageId = $this->_request->getParam('page_id', $this->_request->getParam('id', false));
            if ($pageId)
            {
                return $this->_pageRepository->getById($pageId);
            }
        }
        catch (LocalizedException $e)
        {
            return null;
        }

        return null;
    }

    /**
     * @return Product
     */
    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product') ?: $this->_registry->registry('product');
    }
}
