# Magento 2 Module MgtWizards Auto-Remove Admin Notifications Messages (by shopwhizzy.com)

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)
 - [Specifications](#markdown-header-specifications)
 - [Copyright](#markdown-header-copyright)

## Main Functionalities
Magento 2 admin notifications can be very annoying. This cron will run every hour and remove all unread messages.

## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/MgtWizards/AutoRemoveAdminNotifications`
 - Enable the module by running `php bin/magento module:enable MgtWizards_AutoRemoveAdminNotifications`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

## Specifications

 - Cronjob
	- mgtwizards_autoremoveadminnotifications_removeadminmessages

## Copyright

 - developed by [ShopWhizzy.com](https://shopwhizzy.com/magento)

