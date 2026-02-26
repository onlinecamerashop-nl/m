<?php

namespace Bol\CheckoutViaBol\Observer;

use Bol\CheckoutViaBol\Exception\CvbApiException;
use Bol\CheckoutViaBol\Model\CvbText;
use Bol\CheckoutViaBol\Model\Logger;
use Bol\CheckoutViaBol\Service\ConfigService;
use Bol\CheckoutViaBol\Service\CvbAvailabilityService;
use Bol\CheckoutViaBol\Service\CvbHttpClient;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;

class ValidateMerchantConfigObserver implements ObserverInterface
{
    public function __construct(
        private readonly CvbHttpClient          $cvbHttpClient,
        private readonly ManagerInterface       $messageManager,
        private readonly CvbAvailabilityService $cvbAvailabilityService,
        private readonly ConfigService          $configService,
        private readonly CacheInterface         $cache,
        private readonly Logger                 $logger,
    ) {
    }

    /**
     * @param Observer $observer
     *
     * @return void
     * @throws \JsonException
     */
    public function execute(Observer $observer): void
    {
        if (!$this->cvbAvailabilityService->isShouldValidate()) {
            return;
        }

        $this->logger->info('Validate merchant configuration');
        # Purge cached token to force a new one.
        $this->cache->remove(CvbHttpClient::CACHE_KEY);

        try {
            $this->cvbHttpClient->getToken();
            $this->configService->writeIsTokenValid(true);
            $this->messageManager->addSuccessMessage(CvbText::VALID_MERCHANT_CONFIG_MESSAGE);
        } catch (CvbApiException $e) {
            $this->messageManager->addErrorMessage($this->getErrorMessage($e));
            if ($e->getHttpResponse()->isClientError()) {
                $this->configService->writeIsTokenValid(false);
            }
        }
    }

    private function getErrorMessage(CvbApiException $exception): string
    {
        $response = $exception->getHttpResponse();
        return match (true) {
            $response->isClientError() => CvbText::INVALID_MERCHANT_CONFIG_MESSAGE,
            $response->isServerError() => CvbText::CANNOT_VALIDATE_CONFIG_MESSAGE,
            # We should not hit the default case ...
            default => CvbText::DEFAULT_MERCHANT_CONFIG_ERROR_MESSAGE
        };
    }
}
