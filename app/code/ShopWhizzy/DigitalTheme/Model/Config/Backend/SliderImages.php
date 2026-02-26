<?php
namespace ShopWhizzy\DigitalTheme\Model\Config\Backend;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Config\Model\Config\Backend\Serialized\ArraySerialized;
use Psr\Log\LoggerInterface;

class SliderImages extends ArraySerialized
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Constructor
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param Filesystem $filesystem
     * @param UploaderFactory $uploaderFactory
     * @param LoggerInterface $logger
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        Filesystem $filesystem,
        UploaderFactory $uploaderFactory,
        LoggerInterface $logger,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->filesystem = $filesystem;
        $this->uploaderFactory = $uploaderFactory;
        $this->logger = $logger;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * Process file uploads and update value before saving
     *
     * @return $this
     * @throws LocalizedException
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        $newValue = [];

        // Log $_FILES for debugging
        $this->logger->debug('FILES: ' . print_r($_FILES, true));

        if (is_array($value))
        {
            // Remove '__empty' key as per native ArraySerialized behavior
            unset($value['__empty']);

            foreach ($value as $key => $row)
            {
                if (isset($row['name']) && !empty($row['name']))
                {
                    $newValue[$key]['name'] = $row['name'];

                    // Construct the file input key
                    $fileKey = "groups[{$this->getGroupId()}][fields][slider_images][value][{$key}][file]";

                    // Check if a file was uploaded for this row
                    if (isset($_FILES['groups']['tmp_name'][$this->getGroupId()]['fields']['slider_images']['value'][$key]['file'])
                    && !empty($_FILES['groups']['tmp_name'][$this->getGroupId()]['fields']['slider_images']['value'][$key]['file'])
                    )
                    {
                        try
                        {
                            $uploader = $this->uploaderFactory->create(['fileId' => $fileKey]);
                            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
                            $uploader->setAllowRenameFiles(true);
                            $uploader->setFilesDispersion(false);

                            $mediaDir = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
                            $uploadDir = 'shopwhizzy/slider';

                            // Ensure the upload directory exists
                            $mediaDir->create($uploadDir);

                            $result = $uploader->save($mediaDir->getAbsolutePath($uploadDir));

                            if (isset($result['file']) && $result['file'])
                            {
                                $newValue[$key]['file'] = $uploadDir . '/' . $result['file'];
                            }
                            else
                            {
                                $this->logger->debug("File upload failed for row {$key}: No file saved.");
                            }
                        }
                        catch (\Exception $e)
                        {
                            $this->logger->debug("File upload error for row {$key}: " . $e->getMessage());
                            throw new LocalizedException(__('File upload failed for row %1: %2', $key, $e->getMessage()));
                        }
                    }
                    else
                    {
                        // Retain existing file path if no new file is uploaded
                        $newValue[$key]['file'] = $row['file'] ?? '';
                    }
                }
            }
        }

        $this->logger->debug('Processed Value: ' . print_r($newValue, true));
        $this->setValue($newValue);
        return parent::beforeSave();
    }
}