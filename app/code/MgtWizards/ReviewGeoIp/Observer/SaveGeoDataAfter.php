<?php
declare(strict_types=1);

namespace MgtWizards\ReviewGeoIp\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use MgtWizards\ReviewGeoIp\Helper\GeoIp;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\State;
use Psr\Log\LoggerInterface;

class SaveGeoDataAfter implements ObserverInterface
{
    protected $geoIpHelper;
    protected $request;
    protected $resource;
    protected $logger;
    protected $state;

    public function __construct(
        GeoIp $geoIpHelper,
        Http $request,
        ResourceConnection $resource,
        LoggerInterface $logger,
        State $state
    ) {
        $this->geoIpHelper = $geoIpHelper;
        $this->request = $request;
        $this->resource = $resource;
        $this->logger = $logger;
        $this->state = $state;
    }

    public function execute(Observer $observer)
    {
        try
        {
            // Skip if the request is from the admin area
            if ($this->state->getAreaCode() === \Magento\Framework\App\Area::AREA_ADMINHTML)
            {
                $this->logger->debug('Skipping geolocation data save for admin review save.');
                return;
            }

            $review = $observer->getEvent()->getObject();
            $reviewId = $review->getId();

            if (!$reviewId)
            {
                $this->logger->error('Review ID is missing in SaveGeoDataAfter observer.');
                return;
            }

            // Check if a record already exists for this review_id
            $connection = $this->resource->getConnection();
            $tableName = $this->resource->getTableName('mgtwizards_reviewgeo');
            $select = $connection->select()
                ->from($tableName)
                ->where('review_id = ?', $reviewId);
            $existingRecord = $connection->fetchOne($select);

            if ($existingRecord)
            {
                $this->logger->debug('Geolocation data already exists for review ID: ' . $reviewId . '. Skipping update.');
                return;
            }

            // Get IP address
            $ipHeader = $this->request->getClientIp();
            $ipParts = explode(',', $ipHeader);
            $ipAddress = trim($ipParts[0]);

            // Validate IP address
            if (!filter_var($ipAddress, FILTER_VALIDATE_IP))
            {
                $this->logger->warning('Invalid IP address detected: ' . $ipAddress);
                $ipAddress = '';
                $geoData = ['city' => '', 'country_code' => '', 'country' => ''];
            }
            else
            {
                // Get geo data from IP
                $geoData = $this->geoIpHelper->getGeoData($ipAddress);
            }

            // Prepare data for mgtwizards_reviewgeo
            $data = [
                'review_id' => $reviewId,
                'ip_address' => $ipAddress,
                'city' => $geoData['city'],
                'country' => $geoData['country'],
                'country_code' => $geoData['country_code']
            ];

            // Insert new record
            $this->logger->debug('Inserting geolocation data for review ID: ' . $reviewId);
            $connection->insert($tableName, $data);
        }
        catch (\Exception $e)
        {
            $this->logger->error('Error saving geolocation data to mgtwizards_reviewgeo for review ID ' . ($reviewId ?? 'unknown') . ': ' . $e->getMessage());
        }
    }
}