/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
], function ($) {
    'use strict';

    return function (config, element) {

        const redirect = async function (event) {
            const response = await fetch(config.createSessionUrl);
            const json = await response.json();
            window.location.href = json['redirectUrl'];
        }

        $(element).on('click', CVBCheckoutButton.wrapOnClick(element, redirect));
    }
});
