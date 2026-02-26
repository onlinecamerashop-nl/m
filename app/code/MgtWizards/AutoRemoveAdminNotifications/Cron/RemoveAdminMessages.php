<?php
/**
 * Copyright © shopwhizzy.com All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MgtWizards\AutoRemoveAdminNotifications\Cron;

use Magento\AdminNotification\Model\ResourceModel\Inbox\Collection\Unread;
use Magento\AdminNotification\Model\InboxFactory as InboxModelFactory;

class RemoveAdminMessages
{

    protected $_messages;
    protected $inboxModelFactory;
    protected $logger;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        Unread $messages,
        InboxModelFactory $inboxModelFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_messages = $messages;
        $this->inboxModelFactory = $inboxModelFactory;
        $this->logger = $logger;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        $this->logger->info("Cronjob MgtWizards_RemoveAdminMessages execution started.");

        $items = array_values($this->_messages->getItems());
        try
        {
            foreach ($items as $item)
            {
                $model = $this->inboxModelFactory->create()->load($item->getId());
                if ($model->getId())
                {
                    $model->setIsRemove(1)->save();
                }
            }
            $this->logger->info("Cronjob MgtWizards_RemoveAdminMessages: a total of " . count($items) . " record(s) have been removed.");
        }
        catch (\Magento\Framework\Exception\LocalizedException $e)
        {
            $this->logger->error("Cronjob MgtWizards_RemoveAdminMessages: " . $e->getMessage());
        }
        catch (\Exception $e)
        {
            $this->logger->error("Cronjob MgtWizards_RemoveAdminMessages: we couldn't remove the messages because of an error.");
        }

        $this->logger->info("Cronjob MgtWizards_RemoveAdminMessages execution ended.");
    }
}

