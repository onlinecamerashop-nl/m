<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace MgtWizards\SlideBannerHyva\Block;

class Slider extends \Magento\Framework\View\Element\Template
{
	protected $_filterProvider;
	protected $_sliderFactory;
	protected $_bannerFactory;
	protected $_scopeConfig;
	protected $_storeManager;
	protected $_slider;
	protected $_dir;
	protected $_webpconvertor;

	public function __construct(
	 \Magento\Framework\View\Element\Template\Context $context,
	 \MgtWizards\SlideBannerHyva\Model\SliderFactory $sliderFactory,
	 \MgtWizards\SlideBannerHyva\Model\SlideFactory $slideFactory,
	 \Magento\Cms\Model\Template\FilterProvider $filterProvider,
	 \Magento\Framework\Filesystem\DirectoryList $dir,
	 \WebPConvert\WebPConvert $webpconvertor,
	 array $data = []
	) {
		parent::__construct($context, $data);
		$this->_sliderFactory = $sliderFactory;
		$this->_bannerFactory = $slideFactory;
		$this->_scopeConfig = $context->getScopeConfig();
		$this->_storeManager = $context->getStoreManager();
		$this->_filterProvider = $filterProvider;
		$this->_dir = $dir;
		$this->_webpconvertor = $webpconvertor;
	}

	/**
	 * Prepare Content HTML
	 *
	 * @return string
	 */
	protected function _beforeToHtml()
	{
		$sliderId = $this->getSliderId();
		if ($sliderId && !$this->getTemplate())
		{
			$this->setTemplate("MgtWizards_SlideBannerHyva::slider-wrapper.phtml");
		}
		return parent::_beforeToHtml();
	}

	/**
	 * Return identifiers for produced content
	 *
	 * @return array
	 */
	public function getImageElement($src, $src480 = null, $alt = null, $x = null): string
	{
		$mediaPath = $this->_dir->getPath('media');
		$mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
		$options = [];

		$full_src = $mediaPath . '/' . $src;
		$full_src_webp = $this->getWebpNewUrl($full_src);
		if ($this->needsConversion($full_src, $full_src_webp))
		{
			$this->_webpconvertor->convert($full_src, $full_src_webp, $options);
		}
		$final_src = $this->getFileFilename($full_src_webp);

		$full_src480 = $mediaPath . '/' . $src480;
		$full_src480_webp = $this->getWebpNewUrl($full_src480);
		if ($this->needsConversion($full_src480, $full_src480_webp))
		{
			$this->_webpconvertor->convert($full_src480, $full_src480_webp, $options);
		}
		$final_src480 = $this->getFileFilename($full_src480_webp);

		$isfirstdata = ' class="w-full"';
		if ($alt == null)
		{
			$alt = 'slider';
		}

		if ($x == 1)
		{
			$isfirstdata = ' fetchpriority="high" class="w-full"';
		}
		else
		{
			$isfirstdata = ' loading="lazy" class="w-full"';
		}


		if ($src480 != null)
		{
			return '<picture><source type="image/webp" media="(max-width: 480px)" srcset="' . $mediaUrl . $final_src480 . '"><source type="image/webp" srcset="' . $mediaUrl . $final_src . '"><img' . $isfirstdata . ' width="1600" alt="' . $alt . '" src="' . $mediaUrl . $src . '" class="w-full h-full object-cover" /></picture>';
		}
		else
		{
			return '<picture><source type="image/webp" srcset="' . $mediaUrl . $final_src . '"> <img' . $isfirstdata . ' width="1600" alt="' . $alt . '" src="' . $mediaUrl . $src . '" class="w-full h-full object-cover" /></picture>';
		}
	}

	public function getBannerCollection()
	{
		$sliderId = $this->getSlider()->getId();
		if (!$sliderId)
			return [];
		$collection = $this->_bannerFactory->create()->getCollection();
		$collection->addFieldToFilter('slider_id', $sliderId);
		$collection->addFieldToFilter('slide_status', 1);
		$collection->setOrder('slide_position', 'ASC');
		$collection->setOrder('slide_id', 'ASC');
		$collection->setPageSize(10);
		return $collection;
	}

	public function getSlider()
	{
		if (is_null($this->_slider)):
			$sliderId = $this->getSliderId();
			$this->_slider = $this->_sliderFactory->create();
			$this->_slider->load($sliderId);
		endif;
		return $this->_slider;
	}

	public function getContentText($html)
	{
		$html = $this->_filterProvider->getPageFilter()->filter($html);
		return $html;
	}

	function getWebpNewUrl(string $filename, string $new_extension = 'webp')
	{
		$info = pathinfo($filename);
		return $info['dirname'] . '/' . $info['filename'] . '.' . $new_extension;
	}

	function getFileFilename(string $filename)
	{
		$mediaPath = $this->_dir->getPath('media') . '/';

		$info = pathinfo(str_replace($mediaPath, "", $filename));
		return $info['dirname'] . '/' . $info['filename'] . '.' . $info['extension'];
	}

	public function needsConversion(string $sourceImageFilename, string $destinationImageFilename): bool
	{
		if (file_exists($destinationImageFilename))
		{
			return false;
		}

		return true;
	}

}
