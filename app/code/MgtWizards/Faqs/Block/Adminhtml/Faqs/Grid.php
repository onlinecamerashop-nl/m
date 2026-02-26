<?php

namespace MgtWizards\Faqs\Block\Adminhtml\Faqs;

use Magento\Backend\Block\Widget\Grid as WidgetGrid;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    protected $_collection;

    /**
     * @var \Webkul\Grid\Model\Status
     */
    protected $_status;
    protected $_objectManager;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->_objectManager = $objectManager;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('faqsGrid');
        $this->setDefaultSort('faqs_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('faqs_record');
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_objectManager->create('MgtWizards\Faqs\Model\Faqs', [])->getCollection();
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'faqs_id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'faqs_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'faqs_identifier',
            [
                'header' => __('Identifier'),
                'type' => 'text',
                'index' => 'faqs_identifier',
                'header_css_class' => 'col-identifier',
                'column_css_class' => 'col-identifier'
            ]
        );
        $this->addColumn(
            'faqs_title',
            [
                'header' => __('Title'),
                'type' => 'text',
                'index' => 'faqs_title',
                'header_css_class' => 'col-title',
                'column_css_class' => 'col-title'
            ]
        );
        $this->addColumn(
            'faqs_status',
            [
                'header' => __('Status'),
                'type' => 'options',
                'index' => 'faqs_status',
                'options' => [1 => __('Enable'), 2 => __('Disable')],
                'header_css_class' => 'col-status',
                'column_css_class' => 'col-status'
            ]
        );
        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }
        return parent::_prepareColumns();
    }

    /**
     * @return $this
     */
    // 

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }


    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/*/edit',
            ['faqs_id' => $row->getId()]
        );
    }
}