<?php

namespace Bol\CheckoutViaBol\Service;

use Bol\CheckoutViaBol\Exception\CvbApiException;
use Bol\CheckoutViaBol\Model\Logger;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Locale\Resolver;

class CvbResourceService
{
    private const CACHE_KEY = 'bol_cvb_resources';

    public function __construct(
        private readonly CvbApi         $api,
        private readonly CacheInterface $cache,
        private readonly Logger         $logger,
        private readonly Resolver       $localeResolver,
    )
    {
    }

    public function refreshResources(): array
    {
        $this->logger->debug("refreshing cvb resources");
        $resources = $this->api->getCvbResources();
        $this->logger->debug("received cvb resources", ['response' => $resources]);
        $this->cache->save(json_encode($resources), self::CACHE_KEY, []);
        return $resources;
    }

    private function getLocale(): string
    {
        return str_replace("_", "-", $this->localeResolver->getLocale());
    }

    private function getTextResource($key, $fallback): string
    {
        if ($cached = $this->cache->load(self::CACHE_KEY)) {
            $resources = json_decode($cached);
        } else {
            try {
                $resources = $this->refreshResources();
            } catch (CvbApiException $e) {
                return $fallback;
            }
        }

        $locale = $this->getLocale();

        if (isset($resources->translations->$locale->texts->$key)) {
            return $resources->translations->$locale->texts->$key;
        }

        if (isset($resources->texts->$key)) {
            return $resources->texts->$key;
        }

        return $fallback;
    }

    /**
     * @return string title
     */
    public function getTitle(): string
    {
        return $this->getTextResource('title', 'Checkout via bol');
    }

    /**
     * @return string description
     */
    public function getDescription(): string
    {
        return $this->getTextResource('description', 'Gebruik je bol account om snel en makkelijk de bestelling af te ronden');
    }

    /**
     * @return string explanation that orders will be fulfilled by the webshop itself.
     */
    public function getFulfilledByWebshopText(): string
    {
        return $this->getTextResource('orders-fulfilled-by-merchant', 'De bestelling wordt afgehandeld en geleverd door onze webshop.');
    }

    /**
     * @return string pdp label prefix (short slogan for checkout via bol)
     */
    public function getPdpLabelPrefix(): string
    {
        return $this->getTextResource('pdp-label-prefix', 'Veilig en snel');
    }
}
