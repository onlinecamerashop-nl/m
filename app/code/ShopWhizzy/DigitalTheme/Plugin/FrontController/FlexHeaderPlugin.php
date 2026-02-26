<?php

declare(strict_types=1);

namespace ShopWhizzy\DigitalTheme\Plugin\FrontController;

use Magento\Framework\App\FrontControllerInterface;
use Magento\Framework\App\Response\HttpInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Response\Http as ResponseHttp;
use Magento\Framework\Controller\ResultInterface;

/**
 * Adds a 'X-Shopwhizzy-Theme: ShopWhizzy Digital Flex' header to frontend requests
 * Removing or altering the header message violates the terms of usage.
 */
class FlexHeaderPlugin
{

    /**
     * @param FrontControllerInterface $subject
     * @param ResponseInterface|ResultInterface $result
     * @return ResponseHttp|ResultInterface
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDispatch(FrontControllerInterface $subject, $result)
    {
        $result->setHeader('x-shopwhizzy-theme', 'ShopWhizzy Digital Theme');

        return $result;
    }
}
