<?php


namespace ShopWhizzy\DigitalTheme\Block;

class SubCategories extends \Magento\Framework\View\Element\Template
{

    protected $_storeManager;
    protected $_filesystem;
    protected $_imageFactory;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
    \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        array $data = []
    ) {
        $this->_filesystem = $filesystem;
        $this->_imageFactory = $imageFactory;
        $this->_storeManager = $context->getStoreManager();
        parent::__construct($context, $data);
    }

    public function resize($image, $width = null, $height = null)
    {
        $absolutePath = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath('catalog/category/') . $image; //complete path of image

        $imageResized = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath('resized/' . $width . '/') . $image;
        //create image factory...
        $imageResize = $this->_imageFactory->create();
        $imageResize->open($absolutePath);
        $imageResize->constrainOnly(TRUE);
        $imageResize->keepTransparency(TRUE);
        $imageResize->keepFrame(FALSE);
        $imageResize->keepAspectRatio(TRUE);
        $imageResize->resize($width, $height);
        //destination folder
        $destination = $imageResized;
        //save image
        $imageResize->save($destination);

        $resizedURL = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'resized/' . $width . '/' . $image;

        return $resizedURL;
    }
}
