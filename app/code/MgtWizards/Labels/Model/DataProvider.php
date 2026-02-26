<?php

/**
 * @author MgtWizards Team
 * @copyright Copyright (c) MgtWizards (https://shopwhizzy.com/)
 */

namespace MgtWizards\Labels\Model;

use MgtWizards\Labels\Model\ResourceModel\Label\Collection;
use MgtWizards\Labels\Model\ResourceModel\Label\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var \MgtWizards\Labels\Model\FileInfo
     */
    protected $fileInfo;

    /**
     * DataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param FileInfo $fileInfo
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        \MgtWizards\Labels\Model\FileInfo $fileInfo,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->fileInfo = $fileInfo;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get label's data
     *
     * @return array
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var Label $label */
        foreach ($items as $label) {
            $label->load($label->getId());
            $image = $label->getLabelImage();
            if ($image !== null) {
                $imageData = $this->fileInfo->getUploaderData($image);
                $label->setLabelImage($imageData);
            }
            $this->loadedData[$label->getId()] = $label->getData();
        }

        $data = $this->dataPersistor->get('mgtwizards_label');
        if (!empty($data)) {
            $label = $this->collection->getNewEmptyItem();
            $label->setData($data);
            $this->loadedData[$label->getId()] = $label->getData();
            $this->dataPersistor->clear('mgtwizards_label');
        }

        return $this->loadedData;
    }
}
