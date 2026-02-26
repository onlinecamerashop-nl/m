<?php

namespace MgtWizards\Faqs\Model;

class Faqs extends \Magento\Framework\Model\AbstractModel
{
	/**
	 * Define resource model
	 */
	protected function _construct()
	{
		$this->_init('MgtWizards\Faqs\Model\Resource\Faqs');
	}
	public function getFaqsSetting()
	{
		if (!$this->getData('faqs_setting'))
			return $defaultSetting = array('items' => 1, 'itemsDesktop' => '[1199,1]', 'itemsDesktopSmall' => '[980,3]', 'itemsTablet' => '[768,2]', 'itemsMobile' => '[479,1]', 'faqSpeed' => 500, 'paginationSpeed' => 500, 'rewindSpeed' => 500);
		$data = $this->getData('faqs_setting');
		$data = json_decode($data, true);
		return $data;
	}
	public function getSetting()
	{
		$data = $this->getData('faqs_setting');
		return $data;
	}
}
