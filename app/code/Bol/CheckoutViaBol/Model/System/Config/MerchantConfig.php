<?php

namespace Bol\CheckoutViaBol\Model\System\Config;

use Bol\CheckoutViaBol\Service\CvbAvailabilityService;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value as MagentoConfigValue;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

class MerchantConfig extends MagentoConfigValue
{
    public function __construct(
        private readonly CvbAvailabilityService $cvbAvailabilityService,
        Context                                 $context,
        Registry                                $registry,
        ScopeConfigInterface                    $config,
        TypeListInterface                       $cacheTypeList,
        AbstractResource                        $resource = null,
        AbstractDb                              $resourceCollection = null,
        array                                   $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $resource,
            $resourceCollection,
            $data
        );
    }

    public function afterSave()
    {
        if ($this->isValueChanged()) {
            $this->cvbAvailabilityService->setShouldValidate(true);
        }

        return parent::afterSave();
    }
}
