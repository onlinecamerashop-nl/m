<?php

namespace MgtWizards\Faqs\Block\Adminhtml\Faq;

use Magento\Backend\Block\Widget\Grid as WidgetGrid;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
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
        $this->setId('gridGrid');
        $this->setDefaultSort('faq_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('grid_record');
    }

    protected function _prepareCollection()
    {
        $collection = $this->_objectManager->create('MgtWizards\Faqs\Model\Faq', [])->getCollection()->joinFaqs();
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
            'faqs_id',
            [
                'header' => __('Group'),
                'type' => 'options',
                'index' => 'faqs_id',
                'filter_index' => 'main_table.faqs_id',
                'options' => $this->_getFaqsOptions(),
                'header_css_class' => 'col-group',
                'column_css_class' => 'col-group'
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
        $this->addColumn(
            'faq_position',
            [
                'header' => __('Sort Order'),
                'type' => 'number',
                'index' => 'faq_position',
                'header_css_class' => 'col-position',
                'column_css_class' => 'col-position'
            ]
        );
        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }
        return parent::_prepareColumns();
    }

    protected function _getFaqsOptions()
    {
        $result = [];
        $collection = $this->_objectManager->create('MgtWizards\Faqs\Model\Faqs', [])->getCollection();
        foreach ($collection as $faqs) {
            $result[$faqs->getId()] = $faqs->getFaqsTitle();
        }
        return $result;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }

    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/*/edit',
            ['faq_id' => $row->getId()]
        );
    }
}