define([
    'jquery',
    'Amasty_CheckoutCore/js/model/one-step-layout',
    'Magento_Checkout/js/model/quote'
], function ($, oneStepLayout, quote) {
    'use strict';

    return function (Component) {
        return Component.extend({
            /**
             * Init checkout layout by quote type
             * @returns {void}
             */
            initCheckoutLayout: function () {
                if (!quote.isVirtual()) {
                    oneStepLayout.selectedLayout = window.checkoutConfig.checkoutBlocksConfig;
                } else {
                    oneStepLayout.selectedLayout = oneStepLayout.getVirtualLayout();
                }
                document.querySelectorAll('.opc-wrapper.skeleton').forEach(element => {
                    element.style.display = 'none';
                });
            }
        });
    };
});