<?php /** @noinspection PhpDocMissingThrowsInspection */

namespace Bol\CheckoutViaBol\Service;

use Bol\CheckoutViaBol\Exception\CvbApiException;
use Bol\CheckoutViaBol\Model\Logger;
use Laminas\Http\Client;
use Laminas\Http\Request;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Event\ManagerInterface as EventManager;

class CvbHttpClient
{
    private const USER_AGENT = 'Magento/CvbClient-0.1';
    public const CACHE_KEY = 'bol_token'; // TODO: think about cache key

    public function __construct(
        private readonly Client         $httpClient,
        private readonly ConfigService  $configService,
        private readonly CacheInterface $cache,
        private readonly Logger         $logger,
        private readonly EventManager $eventManager,
    ) {
    }

    /**
     * @param string     $method
     * @param string     $path
     * @param array|null $data
     *
     * @return array|null
     *
     * @throws CvbApiException
     */
    public function request(string $method, string $path, ?array $data = null): ?array
    {
        if ($this->configService->isDebugMode()) {
            $this->logger->logApiCall($method, $path, $data);
        }

        $request = new Request();
        $request->setMethod($method);
        $request->setUri($this->buildPath($path));
        $headers = $request->getHeaders();
        $headers->addHeaders(
            [
                'User-Agent'    => self::USER_AGENT,
                'Authorization' => 'Bearer ' . $this->getToken(),
            ]
        );

        if ($data) {
            $content = json_encode($data, JSON_THROW_ON_ERROR);
            $request->setContent($content);
            $headers->addHeaders(
                [
                    'Content-Type'   => 'application/json',
                    'Content-Length' => strlen($content)
                ]
            );
        } else {
            $headers->addHeaderLine('Content-Length', 0);
        }

        $response = $this->httpClient->send($request);
        if (!$response->isSuccess()) {
            $this->logger->logCvbApiErrorResponse($response);
            throw new CvbApiException($response);
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        return json_decode($response->getBody() ?: '{}', true, 512, JSON_THROW_ON_ERROR);
    }

    private function buildPath(string $path): string
    {
        $baseUrl = $this->configService->isStagingMode()
            ? 'https://api.stg.bol.com/cvb/v1'
            : 'https://api.bol.com/cvb/v1';

        return sprintf('%s/%s', $baseUrl, ltrim($path, '/'));
    }

    /**
     * @return string
     *
     * @throws \JsonException
     * @throws CvbApiException
     */
    public function getToken(): string
    {
        $token = $this->cache->load(self::CACHE_KEY);

        if (!$token) {
            $response = $this->requestToken();
            $token    = $response['access_token'];
            $this->cache->save($token, self::CACHE_KEY, [], (int)$response['expires_in'] - 10);
            $this->eventManager->dispatch('bol_token_obtained', ['token' => $token, 'expires_in' => (int)$response['expires_in']]);
        }

        return $token;
    }

    /**
     * @return array
     *
     * @throws CvbApiException
     */
    private function requestToken(): array
    {
        $id       = $this->configService->getMerchantId();
        $secret   = $this->configService->getMerchantSecret();
        $loginUrl = $this->configService->isStagingMode()
            ? 'https://login.stg.bol.com/token'
            : 'https://login.bol.com/token';

        $request = (new Request())
            ->setMethod('POST')
            ->setUri($loginUrl);
        $request->getQuery()
            ->set('grant_type', 'client_credentials');

        $request->getHeaders()->addHeaders(
            [
                'User-Agent'     => self::USER_AGENT,
                'Authorization'  => 'Basic ' . base64_encode(sprintf('%s:%s', $id, $secret)),
                'Content-Length' => 0
            ]
        );

        $response = $this->httpClient->send($request);
        if (!$response->isSuccess()) {
            $this->logger->logCvbApiErrorResponse($response);
            throw new CvbApiException($response);
        }

        return json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
    }
}
