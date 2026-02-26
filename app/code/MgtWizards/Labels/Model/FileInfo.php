<?php

/**
 * @author MgtWizards Team
 * @copyright Copyright (c) MgtWizards (https://shopwhizzy.com/)
 */

namespace MgtWizards\Labels\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\File\Mime;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class FileInfo
 *
 * Provides information about requested file
 */
class FileInfo
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var Mime
     */
    protected $mime;

    /**
     * @var WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var string[]
     */
    protected $imageFields = ['label_image'];

    /**
     * @param Filesystem $filesystem
     * @param Mime $mime
     * @param StoreManagerInterface $storeManager
     * @param array $imageFields
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\File\Mime $mime,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $imageFields = []
    ) {
        $this->filesystem = $filesystem;
        $this->mime = $mime;
        $this->storeManager = $storeManager;
        $this->imageFields = array_merge($this->imageFields, $imageFields);
    }

    /**
     * MgtWizardss label image to JS uploader format
     *
     * @param string|null $fileName
     * @return array|null
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getUploaderData(?string $fileName)
    {
        $stat = $this->getStat($fileName);
        $mime = $this->getMimeType($fileName);

        $imageData = [];
        // @codingStandardsIgnoreStart
        $imageData[0]['name'] = basename($fileName);
        // @codingStandardsIgnoreEnd
        if ($fileName) {
            $imageData[0]['url'] = $this->getImageUrl($fileName);
        }
        $imageData[0]['size'] = isset($stat) ? $stat['size'] : 0;
        $imageData[0]['type'] = $mime;

        return $imageData;
    }

    /**
     * Builds label image URL
     *
     * @param string $image
     * @param bool $absolute
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getImageUrl(string $image, $absolute = true)
    {
        if ($absolute) {
            $baseMediaUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        } else {
            $baseMediaUrl = '/' . $this->filesystem->getUri(DirectoryList::MEDIA) . '/';
            $baseMediaUrl = str_replace('/pub/', '/', $baseMediaUrl);
        }
        $url = $baseMediaUrl . ltrim($image, '/');
        return $url;
    }

    /**
     * Get media directory
     *
     * @return WriteInterface
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function getMediaDirectory()
    {
        if ($this->mediaDirectory === null) {
            $this->mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        }
        return $this->mediaDirectory;
    }

    /**
     * Retrieve MIME type of requested file
     *
     * @param string $fileName
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getMimeType($fileName)
    {
        $absoluteFilePath = $this->getMediaDirectory()->getAbsolutePath($fileName);
        $result = $this->mime->getMimeType($absoluteFilePath);
        return $result;
    }

    /**
     * Get file statistics data
     *
     * @param string $fileName
     * @return array
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getStat($fileName)
    {
        $result = $this->getMediaDirectory()->stat($fileName);
        return $result;
    }
}
