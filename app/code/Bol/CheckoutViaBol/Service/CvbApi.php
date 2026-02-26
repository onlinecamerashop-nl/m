<?php

namespace Bol\CheckoutViaBol\Service;

use Bol\CheckoutViaBol\Exception\CvbApiException;

class CvbApi
{
    public function __construct(
        private readonly CvbHttpClient $client,
    ) {
    }

    /**
     * @param array $requestData
     *
     * @return array
     *
     * @throws CvbApiException
     */
    public function createCvbSession(array $requestData): array
    {
        return $this->client->request('POST', 'sessions', $requestData);
    }

    /**
     * @param string $sessionId
     * @param string $nonce
     *
     * @return array
     *
     * @throws CvbApiException
     */
    public function getCvbSession(string $sessionId, string $nonce): array
    {
        return $this->client->request(
            'GET',
            sprintf(
                'sessions/%s?%s',
                $sessionId,
                http_build_query(['nonce' => $nonce])
            )
        );
    }

    /**
     * @param array $supportedLocales
     *
     * @return array
     *
     * @throws CvbApiException
     */
    public function getCvbResources(array $supportedLocales = []): array
    {
        $path = 'resources';

        if (!empty($supportedLocales)) {
            $path = sprintf('%s?%s', $path, http_build_query(["locales" => $supportedLocales]));
        }

        return $this->client->request('GET', $path);
    }

    /**
     * @param array $orderRequestData
     *
     * @return array
     *
     * @throws CvbApiException
     */
    public function createOrder(array $orderRequestData): array
    {
        return $this->client->request(
            'POST',
            'orders',
            $orderRequestData
        );
    }

    /**
     * @param string $bolOrderReference
     * @param array  $event
     *
     * @return array
     *
     * @throws CvbApiException
     */
    public function createOrderEvent(string $bolOrderReference, array $event): array
    {
        return $this->client->request(
            'POST',
            sprintf('orders/%s/events', $bolOrderReference),
            $event
        );
    }

    /**
     * Returns an empty array on succes
     * @param array $stats
     *
     * @return array
     *
     * @throws CvbApiException
     */
    public function postMerchantStats(array $stats): array
    {
        return $this->client->request(
            'POST',
            'merchants/me/stats',
            $stats
        );
    }
}
