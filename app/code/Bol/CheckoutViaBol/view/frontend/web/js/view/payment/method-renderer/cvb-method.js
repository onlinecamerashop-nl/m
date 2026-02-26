define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/action/redirect-on-success',
        'mage/url'
    ],
    function ($, Component, redirectOnSuccessAction, url) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Bol_CheckoutViaBol/payment/cvb'
            },

            /**
             * Redirect to controller after place order
             */
            afterPlaceOrder: function () {
                redirectOnSuccessAction.redirectUrl = url.build('cvb/order/redirect/');
                this.redirectAfterPlaceOrder = true;
            },

            getIcon: function() {
                return window.checkoutConfig.cvb.paymentLogo;
            }
        });
    }
);
