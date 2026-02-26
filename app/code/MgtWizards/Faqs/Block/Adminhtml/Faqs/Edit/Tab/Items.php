<?php

namespace MgtWizards\Faqs\Block\Adminhtml\Faqs\Edit\Tab;

use Magento\Backend\Block\Widget\Grid as WidgetGrid;

class Items extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $moduleManager;
    protected $_collection;
    protected $_status;
    protected $_objectManager;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->_objectManager = $objectManager;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('faqGrid');
        $this->setDefaultSort('faq_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('grid_record');
    }

    protected function _prepareCollection()
    {
        $collection = $this->_objectManager->create('MgtWizards\Faqs\Model\Faq', [])->getCollection();
        $collection->addFieldToFilter('faqs_id', $this->getRequest()->getParam('faqs_id'));
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'faq_id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'faq_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'faq_question',
            [
                'header' => __('Question'),
                'type' => 'text',
                'index' => 'faq_question',
                'header_css_class' => 'col-question',
                'column_css_class' => 'col-question'
            ]
        );
        $this->addColumn(
            'faq_status',
            [
                'header' => __('Status'),
                'type' => 'options',
                'index' => 'faq_status',
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

    public function getGridUrl()
    {
        return $this->getUrl('*/*/faqgrid', ['_current' => true]);
    }

    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/faq/edit',
            ['faq_id' => $row->getId(), 'faqs_id' => $this->getRequest()->getParam('faqs_id')]
        );
    }
}