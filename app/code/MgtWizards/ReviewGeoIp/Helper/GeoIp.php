<?php
namespace MgtWizards\ReviewGeoIp\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;

class GeoIp extends AbstractHelper
{
    protected $curl;
    protected $logger;
    protected $resource;

    public function __construct(
        Context $context,
        Curl $curl,
        LoggerInterface $logger,
        ResourceConnection $resource
    ) {
        $this->curl = $curl;
        $this->logger = $logger;
        $this->resource = $resource;
        parent::__construct($context);
    }

    /**
     * Fetch geolocation data from ip-api.com for a given IP address
     *
     * @param string|null $ipAddress
     * @return array ['city' => string, 'country_code' => string]
     */
    public function getGeoData(?string $ipAddress): array
    {
        if (!$ipAddress || !filter_var($ipAddress, FILTER_VALIDATE_IP))
        {
            $this->logger->warning('Invalid or empty IP address provided to GeoIP: ' . ($ipAddress ?: 'null'));
            return ['city' => '', 'country_code' => ''];
        }

        try
        {
            $url = "http://ip-api.com/json/{$ipAddress}";
            $this->curl->setOption(CURLOPT_SSL_VERIFYPEER, false);
            $this->curl->setOption(CURLOPT_SSL_VERIFYHOST, false);
            $this->curl->get($url);
            $response = json_decode($this->curl->getBody(), true);

            $this->logger->info('GeoIP API response for IP ' . $ipAddress . ': ' . print_r($response, true));

            if ($response && isset($response['status']) && $response['status'] === 'success')
            {
                return [
                    'city' => $response['city'] ?? '',
                    'country' => $response['country'] ?? '',
                    'country_code' => $response['countryCode'] ?? ''
                ];
            }
            else
            {
                $errorMessage = $response['message'] ?? 'Unknown error';
                $this->logger->warning('GeoIP API failed for IP ' . $ipAddress . ': ' . $errorMessage);
            }
        }
        catch (\Exception $e)
        {
            $this->logger->error('GeoIP API error for IP ' . $ipAddress . ': ' . $e->getMessage());
        }

        return ['city' => '', 'country' => '', 'country_code' => ''];
    }

    /**
     * Fetch geolocation data from mgtwizards_reviewgeo table by review ID
     *
     * @param int $reviewId
     * @return array ['ip_address' => string, 'city' => string, 'country_code' => string]
     * @throws \InvalidArgumentException
     */
    public function getGeoDataByReviewId(int $reviewId): array
    {
        if ($reviewId <= 0)
        {
            $this->logger->warning('Invalid review ID provided to getGeoDataByReviewId: ' . $reviewId);
            throw new \InvalidArgumentException('Review ID must be a positive integer.');
        }

        try
        {
            $connection = $this->resource->getConnection();
            $tableName = $this->resource->getTableName('mgtwizards_reviewgeo');

            $select = $connection->select()
                ->from($tableName, ['ip_address', 'city', 'country', 'country_code'])
                ->where('review_id = ?', $reviewId);
            $result = $connection->fetchRow($select);

            if ($result)
            {
                return [
                    'ip_address' => $result['ip_address'] ?? '',
                    'city' => $result['city'] ?? '',
                    'country' => $result['country'] ?? '',
                    'country_code' => $result['country_code'] ?? ''
                ];
            }

            $this->logger->info('No geolocation data found for review ID: ' . $reviewId);
        }
        catch (\Exception $e)
        {
            $this->logger->error('Error fetching geolocation data for review ID ' . $reviewId . ': ' . $e->getMessage());
        }

        return ['ip_address' => '', 'city' => '', 'country' => '', 'country_code' => ''];
    }
}