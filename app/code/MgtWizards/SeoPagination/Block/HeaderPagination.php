<?php
/**
 * Copyright © 2020-present ShopWhizzy. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MgtWizards\SeoPagination\Block;

use Magento\Framework\View\Element\Template;

/**
 * HeaderPagination block for adding SEO-friendly pagination links to category pages
 *
 * To include in header, add this block in layout file:
 * app/code/MgtWizards/SeoPagination/view/frontend/layout/catalog_category_view.xml
 */
class HeaderPagination extends Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Catalog\Model\Category|null
     */
    protected $category;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    protected $toolbar;

    /**
     * @var \Magento\Theme\Block\Html\Pager
     */
    protected $pager;

    /**
     * HeaderPagination constructor
     *
     * @param Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Block\Product\ProductList\Toolbar $toolbar
     * @param \Magento\Theme\Block\Html\Pager $pager
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Block\Product\ProductList\Toolbar $toolbar,
        \Magento\Theme\Block\Html\Pager $pager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->category = $registry->registry('current_category');
        $this->toolbar = $toolbar;
        $this->pager = $pager;
        $this->logger = $context->getLogger();

        if (!$this->category)
        {
            $this->logger->warning('No current category found in registry for SEO pagination');
        }
    }

    /**
     * Generate pagination header links for SEO
     *
     * @return string
     */
    public function getPageHeaders(): string
    {
        if (!$this->category)
        {
            return '';
        }

        $output = '';
        $this->toolbar->setCollection($this->category->getProductCollection());
        $this->pager->setCollection($this->category->getProductCollection())
            ->setShowPerPage($this->toolbar->getLimit());

        $totalProducts = $this->category->getProductCollection()->count();
        $itemsPerPage = $this->toolbar->getLimit();
        $lastPage = (int)ceil($totalProducts / $itemsPerPage);
        $currentPage = $this->toolbar->getCurrentPage();

        $params = $this->getRequest()->getParams();
        unset($params['id']); // Remove category ID param

        // Generate previous page link
        if ($currentPage > 1 && $lastPage > 1)
        {
            $params[$this->pager->getPageVarName()] = $currentPage - 1;
            $prevPageUrl = $this->toolbar->getPagerUrl($params);
            $output .= "<link rel=\"prev\" href=\"{$prevPageUrl}\">" . PHP_EOL;
        }

        // Generate next page link
        if ($currentPage < $lastPage)
        {
            $params[$this->pager->getPageVarName()] = $currentPage + 1;
            $nextPageUrl = $this->toolbar->getPagerUrl($params);
            $output .= "<link rel=\"next\" href=\"{$nextPageUrl}\">" . PHP_EOL;
        }

        return $output;
    }
}