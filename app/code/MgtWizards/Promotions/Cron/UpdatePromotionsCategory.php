<?php
/**
 * Copyright © MgtWizards. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MgtWizards\Promotions\Cron;

use Psr\Log\LoggerInterface;
use MgtWizards\Promotions\Helper\Data;

class UpdatePromotionsCategory
{
  protected $logger;
  protected $_dataHelper;

  /**
   * Constructor
   *
   * @param LoggerInterface $logger
   * @param Data $dataHelper
   */
  public function __construct(
      LoggerInterface $logger,
      Data $dataHelper
  ) {
    $this->logger = $logger;
    $this->_dataHelper = $dataHelper;
  }

  /**
   * Update products in the promotions category
   *
   * @return void
   */
  public function execute()
  {
    try
    {
      $this->_dataHelper->syncPromotionsCategory();
    }
    catch (\Exception $e)
    {
      $this->logger->error("Error in promotions category cron: " . $e->getMessage());
    }
  }
}