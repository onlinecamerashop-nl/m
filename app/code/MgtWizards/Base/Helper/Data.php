<?php
/**
 * Copyright © shopwhizzy.com All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MgtWizards\Base\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Filesystem\DirectoryList;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\CacheInterface;

class Data extends AbstractHelper
{
    protected $_filesystem;
    protected $_imageFactory;
    protected $_directory;
    protected $_logger;
    protected $_cache;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Image\AdapterFactory $imageFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        LoggerInterface $logger,
        CacheInterface $cache,
        array $data = []
    ) {
        $this->_filesystem = $filesystem;
        $this->_imageFactory = $imageFactory;
        $this->_directory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->_logger = $logger;
        $this->_cache = $cache;
        parent::__construct($context);
    }

    /**
     * Get array of icon file names (without .svg extension) from the theme SVG directory
     *
     * @return array
     */
    public function getThemeIconNames(): array
    {
        $cacheKey = 'mgtwizards_theme_icon_names';
        $cached = $this->_cache->load($cacheKey);
        if ($cached !== false)
        {
            return unserialize($cached);
        }

        $iconNames = [];
        $possiblePaths = [
            'app/code/MgtWizards/Base/view/frontend/web/svg/theme',
            'vendor/mgtwizards/base/view/frontend/web/svg/theme'
        ];

        foreach ($possiblePaths as $relativePath)
        {
            try
            {
                $basePath = rtrim($this->_filesystem->getDirectoryRead(DirectoryList::ROOT)
                    ->getAbsolutePath($relativePath), '/');

                if (is_dir($basePath))
                {
                    $files = scandir($basePath);
                    foreach ($files as $file)
                    {
                        if (is_file($basePath . '/' . $file)
                        && strtolower(pathinfo($file, PATHINFO_EXTENSION)) === 'svg')
                        {
                            $iconNames[] = pathinfo($file, PATHINFO_FILENAME);
                        }
                    }
                }
            }
            catch (\Exception $e)
            {
                // Silently continue
            }
        }

        $iconNames = array_unique($iconNames);
        sort($iconNames, SORT_STRING | SORT_FLAG_CASE);

        $this->_cache->save(serialize($iconNames), $cacheKey, ['MGTWIZARDS_ICONS'], 86400);

        return $iconNames;
    }

    /**
     * Get array of icon file names (without .svg extension) from the theme SVG directory
     *
     * @return array
     */
    public function getPaymentIconNames(): array
    {
        $cacheKey = 'mgtwizards_payment_icon_names';
        $cached = $this->_cache->load($cacheKey);
        if ($cached !== false)
        {
            //return unserialize($cached);
        }

        $iconNames = [];
        $possiblePaths = [
            'app/design/frontend/ShopWhizzy/digital/Hyva_PaymentIcons/web/svg/payment-icons/light',
            'vendor/hyva-themes/magento2-payment-icons/src/view/frontend/web/svg/payment-icons/light'
        ];

        foreach ($possiblePaths as $relativePath)
        {
            try
            {
                $basePath = rtrim($this->_filesystem->getDirectoryRead(DirectoryList::ROOT)
                    ->getAbsolutePath($relativePath), '/');

                if (is_dir($basePath))
                {
                    $files = scandir($basePath);
                    foreach ($files as $file)
                    {
                        if (is_file($basePath . '/' . $file)
                        && strtolower(pathinfo($file, PATHINFO_EXTENSION)) === 'svg')
                        {
                            $iconNames[] = pathinfo($file, PATHINFO_FILENAME);
                        }
                    }
                }
            }
            catch (\Exception $e)
            {
                // Silently continue
            }
        }

        $iconNames = array_unique($iconNames);
        sort($iconNames, SORT_STRING | SORT_FLAG_CASE);

        $this->_cache->save(serialize($iconNames), $cacheKey, ['MGTWIZARDS_ICONS'], 86400);

        return $iconNames;
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
        $imageResize->constrainOnly(TRUE);
        $imageResize->keepTransparency(TRUE);
        $imageResize->keepFrame(FALSE);
        $imageResize->keepAspectRatio(TRUE);
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
            if ((is_null($specialPriceFromDate) && is_null($specialPriceToDate)) || ($today >= strtotime($specialPriceFromDate) && is_null($specialPriceToDate)) || ($today <= strtotime($specialPriceToDate) && is_null($specialPriceFromDate)) || ($today >= strtotime($specialPriceFromDate) && $today <= strtotime($specialPriceToDate)))
            {
                return true;
            }
        }
        return false;
    }

}
