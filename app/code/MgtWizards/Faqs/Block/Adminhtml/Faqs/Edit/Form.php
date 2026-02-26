<?php

namespace MgtWizards\Faqs\Block\Adminhtml\Faqs\Edit;

use Magento\Backend\Block\Widget\Form\Generic;

class Form extends Generic
{
    /**
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id'    => 'edit_form',
                    'action' => $this->getUrl('*/*/save', ['faqs_id' => $this->getRequest()->getParam('faqs_id')]),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data',
                ]
            ]
        );
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
