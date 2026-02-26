# Mage2 Module MgtWizards SeoPagination

    ``mgtwizards/module-seopagination``

 - [Main Functionalities](#main-functionalities)
 - [Installation](#installation)
 - [Configuration](#configuration)
 - [Specifications](#specifications)
 - [Attributes](#attributes)

## Main Functionalities
This module enhances SEO for Magento 2 category pages by adding prev/next pagination links to the page header. These links help search engines understand the pagination structure, improving crawlability and indexation.

Key features:
- Automatically generates `<link rel="prev">` and `<link rel="next">` tags
- Integrates with Magento's category product collection
- Works with existing toolbar and pager components
- Lightweight implementation with minimal performance impact

## Installation
* = in production please use the `--keep-generated` option

### Type 1: Zip file
- Unzip the zip file in `app/code/MgtWizards`
- Enable the module: `php bin/magento module:enable MgtWizards_SeoPagination`
- Apply database updates: `php bin/magento setup:upgrade`*
- Flush cache: `php bin/magento cache:flush`

### Type 2: Composer
- Add the composer repository: `composer config repositories.repo.magento.com composer https://repo.magento.com/`
- Install the module: `composer require mgtwizards/module-seopagination`
- Enable the module: `php bin/magento module:enable MgtWizards_SeoPagination`
- Apply database updates: `php bin/magento setup:upgrade`*
- Flush cache: `php bin/magento cache:flush`

## Configuration
No additional configuration is required. The module automatically adds pagination links to category pages after installation.

## Specifications
- Block
  - HeaderPagination > headerpagination.phtml
- Layout
  - catalog_category_view.xml

## Attributes
None