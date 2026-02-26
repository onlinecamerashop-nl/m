<?php

namespace Bol\CheckoutViaBol\Cron;

use Bol\CheckoutViaBol\Exception\CvbApiException;
use Bol\CheckoutViaBol\Service\CvbStatsService;
use DateMalformedStringException;
use DateTimeImmutable;
use Magento\Framework\FlagManager;

class MerchantStatsCron
{
    private const ERROR_DATES_FLAG = 'cvb_stats_error_dates';

    public function __construct(
        private readonly FlagManager     $flagManager,
        private readonly CvbStatsService $cvbStatsService,
    ) {
    }

    public function execute(): void
    {
        $errors = [];
        foreach ($this->getDates() as $date) {
            try {
                $this->cvbStatsService->pushStats(new DateTimeImmutable($date));
            } catch (CvbApiException $e) {
                $errors[] = $date;
            } catch (DateMalformedStringException $exception) {
                # This should not happen but in theory some other process could alter the flag table so that
                # one of the values is no longer recognizable as a date string
                continue;
            }
        }

        $this->saveErrors($errors);
    }

    /**
     * @return string[]
     */
    private function getDates(): array
    {
        $yesterday  = (new DateTimeImmutable('-1 day'))->format('Y-m-d');
        $errorDates = $this->getErrorDates();
        return array_unique(
            array_merge($errorDates, [$yesterday])
        );
    }

    private function getErrorDates(): array
    {
        return $this->flagManager->getFlagData(self::ERROR_DATES_FLAG)
            ?: [];
    }

    /**
     * @param string[] $errorDates
     *
     * @return void
     */
    private function saveErrors(array $errorDates): void
    {
        $this->flagManager->saveFlag(self::ERROR_DATES_FLAG, $errorDates);
    }
}
