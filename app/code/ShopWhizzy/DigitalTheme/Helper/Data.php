<?php
declare(strict_types=1);

namespace ShopWhizzy\DigitalTheme\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Review\Model\ResourceModel\Review\Summary\CollectionFactory as ReviewSummaryCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Api\CategoryRepositoryInterface;

class Data extends AbstractHelper
{
    const XML_CONFIG_PATH = 'digitaltheme_settings';
    const XML_ENABLED = 'general/enable';

    protected $_filesystem;
    protected $_imageFactory;
    protected $_directory;
    public $scopeConfig;
    protected $productRepository;
    protected $reviewSummaryCollectionFactory;
    protected $storeManager;
    protected $productCollectionFactory;
    protected $categoryRepository;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Image\AdapterFactory $imageFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Review\Model\ResourceModel\Review\Summary\CollectionFactory $reviewSummaryCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        ProductRepositoryInterface $productRepository,
        ReviewSummaryCollectionFactory $reviewSummaryCollectionFactory,
        StoreManagerInterface $storeManager,
        ProductCollectionFactory $productCollectionFactory,
        CategoryRepositoryInterface $categoryRepository,
        array $data = []
    ) {
        $this->_filesystem = $filesystem;
        $this->_imageFactory = $imageFactory;
        $this->_directory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->scopeConfig = $scopeConfig;
        $this->productRepository = $productRepository;
        $this->reviewSummaryCollectionFactory = $reviewSummaryCollectionFactory;
        $this->storeManager = $storeManager;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->categoryRepository = $categoryRepository;
        parent::__construct($context);
    }

    /**
     * Get product collection for a category, sorted by newest first
     *
     * @param int $categoryId
     * @param int $productCount
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getCategoryItems($categoryId, $productCount = 12)
    {
        try
        {
            $category = $this->categoryRepository->get($categoryId);
        }
        catch (\Magento\Framework\Exception\NoSuchEntityException $e)
        {
            return []; // Or throw exception
        }

        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*')
            ->addCategoryFilter($category)
            ->addAttributeToFilter('status', Status::STATUS_ENABLED)
            ->addAttributeToFilter('visibility', [
                'in' => [
                    Visibility::VISIBILITY_BOTH,
                    Visibility::VISIBILITY_IN_CATALOG,
                    Visibility::VISIBILITY_IN_SEARCH
                ]
            ])
            ->setOrder('created_at', 'DESC')
            ->setPageSize($productCount)
            ->setCurPage(1);

        return $collection;
    }

    /**
     * Get slider images with names and paths
     *
     * @return array
     */
    public function getSliderImages(): array
    {
        $configValue = $this->getConfigValue('general/slider_images');
        $sliderImages = $configValue ? json_decode($configValue, true) : [];
        $mediaUrl = $this->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $mediaDirectory = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);

        $result = [];
        foreach ($sliderImages as $image)
        {
            if (isset($image['name']) && isset($image['path']) && $mediaDirectory->isFile($image['path']))
            {
                $result[] = [
                    'name' => $image['name'],
                    'path' => $mediaUrl . $image['path']
                ];
            }
        }

        return $result;
    }

    public function resizedImage($image, $path = 'resized', $width = null, $height = null)
    {
        $realPath = $image;
        if (!$this->_directory->isFile($realPath) || !$this->_directory->isExist($realPath))
        {
            return false;
        }
        $targetDir = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath($path . '/' . $width . 'x' . $height);
        $pathTargetDir = $this->_directory->getRelativePath($targetDir);

        if (!$this->_directory->isExist($pathTargetDir))
        {
            $this->_directory->create($pathTargetDir);
        }
        if (!$this->_directory->isExist($pathTargetDir))
        {
            return false;
        }
        $imageResize = $this->_imageFactory->create();
        $imageResize->open($realPath);
        $imageResize->keepAspectRatio(true);
        $imageResize->constrainOnly(true);
        $imageResize->keepTransparency(true);
        $imageResize->keepFrame(false);
        $imageResize->resize($width, $height);
        $destination = $targetDir . '/' . pathinfo($realPath, PATHINFO_BASENAME);
        $imageResize->save($destination);
        if ($this->_directory->isFile($this->_directory->getRelativePath($destination)))
        {
            return $this->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $path . '/' . $width . 'x' . $height . '/' . $image;
        }
        return false;
    }

    public function checkProductIsNew($_product = null)
    {
        $from_date = $_product->getNewsFromDate();
        $to_date = $_product->getNewsToDate();
        $is_new = false;
        $today = strtotime("now");
        if ($from_date && $to_date)
        {
            $from_date = strtotime($from_date);
            $to_date = strtotime($to_date);
            if ($from_date <= $today && $to_date >= $today)
            {
                $is_new = true;
            }
        }
        elseif ($from_date && !$to_date)
        {
            $from_date = strtotime($from_date);
            if ($from_date <= $today)
            {
                $is_new = true;
            }
        }
        elseif (!$from_date && $to_date)
        {
            $to_date = strtotime($to_date);
            if ($to_date >= $today)
            {
                $is_new = true;
            }
        }
        return $is_new;
    }

    public function checkProductIsSale($_product = null)
    {
        $specialprice = $_product->getSpecialPrice();
        $oldPrice = $_product->getPrice();

        $specialPriceFromDate = $_product->getSpecialFromDate();
        $specialPriceToDate = $_product->getSpecialToDate();
        $today = time();
        if ($specialprice < $oldPrice && $specialprice)
        {
            if ((is_null($specialPriceFromDate) && is_null($specialPriceToDate)) ||
            ($today >= strtotime($specialPriceFromDate) && is_null($specialPriceToDate)) ||
            ($today <= strtotime($specialPriceToDate) && is_null($specialPriceFromDate)) ||
            ($today >= strtotime($specialPriceFromDate) && $today <= strtotime($specialPriceToDate)))
            {
                return true;
            }
        }
        return false;
    }

    public function getConfigValue($path)
    {
        $value = $this->scopeConfig->getValue(
            self::XML_CONFIG_PATH . '/' . $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        // Check if value matches rgb(x,x,x) format
        if (is_string($value) && preg_match('/^rgb\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*\)$/', $value, $matches))
        {
            // Convert RGB to HEX
            $r = max(0, min(255, (int)$matches[1]));
            $g = max(0, min(255, (int)$matches[2]));
            $b = max(0, min(255, (int)$matches[3]));
            return sprintf('#%02x%02x%02x', $r, $g, $b);
        }

        return $value;
    }

    /**
     * Get base URL for media directory
     *
     * @param string $type
     * @return string
     */
    protected function getBaseUrl(string $type): string
    {
        return $this->_urlBuilder->getBaseUrl(['_type' => $type]);
    }

    /**
     * Check if product has an average rating meeting the configured threshold with minimum reviews
     *
     * @param \Magento\Catalog\Model\Product|int|null $product
     * @return bool
     */
    public function isHotRated($product = null): bool
    {
        // Check if hot rated feature is enabled
        $isEnabled = $this->scopeConfig->isSetFlag(
            self::XML_CONFIG_PATH . '/products_listing/enable_hot_rated',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (!$isEnabled || !$product)
        {
            return false;
        }

        // If product is an ID, load the product
        if (is_numeric($product))
        {
            try
            {
                $product = $this->productRepository->getById($product);
            }
            catch (\Exception $e)
            {
                return false;
            }
        }

        // Ensure we have a valid product object
        if (!$product instanceof \Magento\Catalog\Model\Product)
        {
            return false;
        }

        // Get configuration values
        $minRating = (float)$this->getConfigValue('products_listing/hot_rated_min_rating') ?: 4.5;
        $minReviews = (int)$this->getConfigValue('products_listing/hot_rated_min_reviews') ?: 2;

        // Check if review data is available in the product object
        $ratingSummary = $product->getRatingSummary();
        $reviewCount = $product->getReviewsCount();

        if (!$ratingSummary || !$reviewCount)
        {
            // Load review summary if not available
            try
            {
                $storeId = $this->storeManager->getStore()->getId();
                $summary = $this->reviewSummaryCollectionFactory->create()
                    ->addFieldToFilter('entity_pk_value', $product->getId())
                    ->addFieldToFilter('store_id', $storeId)
                    ->getFirstItem();
                $ratingSummary = $summary->getRatingSummary();
                $reviewCount = $summary->getReviewsCount();
            }
            catch (\Exception $e)
            {
                // Log the error for debugging (optional, requires Psr\Log\LoggerInterface injection)
                // $this->_logger->error('Error loading review summary: ' . $e->getMessage());
                return false;
            }
        }

        if (!$ratingSummary || !$reviewCount)
        {
            return false;
        }

        // Convert configured minimum rating to Magento's percentage scale (0-100)
        // Rating summary is a percentage, so multiply minRating (0-5 scale) by 20
        $minRatingPercentage = $minRating * 20;

        return $ratingSummary >= $minRatingPercentage && $reviewCount >= $minReviews;
    }
}