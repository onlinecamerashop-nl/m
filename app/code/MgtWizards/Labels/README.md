# MgtWizards Labels

## Features

- Admin creates labels based on product attribute and price conditions
- Only product attributes used in listing are allowed
- No indexer is used, calculation is performed on the fly

## Installation and Configuration

Update **/dev/tools/frontools/config/themes.json**

`"MgtWizards_Labels": "vendor/mgtwizards/module-labels/view/frontend"`

Update **/app/design/frontend/MgtWizards/{name}/styles/styles.scss**

`@import "../MgtWizards_Labels/styles/module";`

## Dependencies

- Labels v2.* requires Magento v2.3, v2.4.