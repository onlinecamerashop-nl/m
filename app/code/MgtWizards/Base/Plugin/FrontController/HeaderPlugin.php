<?php

declare(strict_types=1);

namespace MgtWizards\Base\Plugin\FrontController;

use Magento\Framework\App\FrontControllerInterface;
use Magento\Framework\App\Response\HttpInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Response\Http as ResponseHttp;
use Magento\Framework\Controller\ResultInterface;

/**
 * Adds a 'X-Shopwhizzy: ShopWhizzy' header to frontend requests
 * Removing or altering the header message violates the terms of usage.
 */
class HeaderPlugin
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
        $result->setHeader('x-shopwhizzy', 'ShopWhizzy');

        return $result;
    }
}
