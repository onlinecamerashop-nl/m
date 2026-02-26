<?php

/**
 * @author MgtWizards Team
 * @copyright Copyright (c) MgtWizards (https://shopwhizzy.com/)
 */

namespace MgtWizards\Labels\Controller\Adminhtml\Index;

use MgtWizards\Labels\Model\Label;
use MgtWizards\Labels\Model\LabelRepository;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\ImageProcessor;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use \Magento\Framework\Api\Data\ImageContentInterfaceFactory;
use \Magento\Framework\Filesystem\DirectoryList;

/**
 * Save action for label
 */
class Save extends \Magento\Backend\App\Action implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'MgtWizards_Labels::labels';

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var LabelRepository
     */
    protected $labelRepository;

    /**
     * @var ImageContentInterfaceFactory
     */
    protected $imageContentFactory;

    /**
     * Image processor
     * @var ImageProcessor
     */
    protected $imageProcessor;

    /**
     * @var DirectoryList
     */
    protected $directory;

    /**
     * Destination folder
     * @var string
     */
    protected $destinationFolder = 'mgtwizards/label';

    /**
     * Save constructor.
     * @param LabelRepository $labelRepository
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param ImageContentInterfaceFactory $imageContentFactory
     * @param ImageProcessor $imageProcessor
     * @param DirectoryList $directory
     */
    public function __construct(
        LabelRepository $labelRepository,
        Context $context,
        DataPersistorInterface $dataPersistor,
        ImageContentInterfaceFactory $imageContentFactory,
        ImageProcessor $imageProcessor,
        DirectoryList $directory
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->labelRepository = $labelRepository;
        $this->imageContentFactory = $imageContentFactory;
        $this->imageProcessor = $imageProcessor;
        $this->directory = $directory;
        parent::__construct($context);
    }

    /**
     * Execute save action from catalog rule
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            /** @var Label $model */
            $model = $this->_objectManager->create(Label::class);

            try {
                $data = $this->getRequest()->getPostValue();

                $id = $this->getRequest()->getParam('label_id');
                if ($id) {
                    $model = $this->labelRepository->get($id);
                }

                $validateResult = $model->validateData(new \Magento\Framework\DataObject($data));
                if ($validateResult !== true) {
                    foreach ($validateResult as $errorMessage) {
                        $this->messageManager->addErrorMessage($errorMessage);
                    }
                    $this->_getSession()->setPageData($data);
                    $this->dataPersistor->set('mgtwizards_label', $data);
                    $this->_redirect('mgtwizards_labels/*/edit', ['id' => $model->getId()]);
                    return;
                }

                if (isset($data['rule'])) {
                    $data['conditions'] = $data['rule']['conditions'];
                    unset($data['rule']);
                }

                unset($data['conditions_serialized']);
                unset($data['actions_serialized']);

                if (isset($data['label_image'][0]['url'])) {
                    $file = $data['label_image'][0];
                    $tmpFilePath = $this->directory->getPath('pub') . parse_url($file['url'])['path'];
                    //@codingStandardsIgnoreStart
                    $content = base64_encode(file_get_contents($tmpFilePath));
                    //@codingStandardsIgnoreEnd
                    /** @param ImageContentInterfaceFactory $imageContent */
                    $imageContent = $this->imageContentFactory->create();
                    $imageContent->setType($file['type'])
                        ->setName($file['name'])
                        ->setBase64EncodedData($content);

                    try {
                        $filePath = $this->imageProcessor->processImageContent($this->destinationFolder, $imageContent);
                        $data['label_image'] = $this->destinationFolder . $filePath;
                    } catch (\Exception $e) {
                        $this->messageManager->addErrorMessage($e->getMessage());
                        $this->_redirect('mgtwizards_labels/*/edit', ['id' => $model->getId()]);
                        return;
                    }
                } else {
                    $data['label_image'] = '';
                }

                $model->loadPost($data);

                $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setPageData($data);
                $this->dataPersistor->set('mgtwizards_label', $data);

                $this->labelRepository->save($model);

                $this->messageManager->addSuccessMessage(__('You saved the label.'));
                $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setPageData(false);
                $this->dataPersistor->clear('mgtwizards_label');

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('mgtwizards_labels/*/edit', ['id' => $model->getId()]);
                    return;
                }
                $this->_redirect('mgtwizards_labels/*/');

                return;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Something went wrong while saving the label data. Please review the error log.')
                );
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
                $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setPageData($data);
                $this->dataPersistor->set('mgtwizards_label', $data);
                $this->_redirect('mgtwizards_labels/*/edit', ['id' => $this->getRequest()->getParam('label_id')]);
                return;
            }
        }
        $this->_redirect('mgtwizards_labels/*/');
    }
}
