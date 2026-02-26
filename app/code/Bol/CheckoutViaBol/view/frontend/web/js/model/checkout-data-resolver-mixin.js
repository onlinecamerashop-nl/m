define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/checkout-data',
    'Bol_CheckoutViaBol/js/model/cvb-refresh',
    'Magento_Checkout/js/model/payment-service',
], function (
    $,
    wrapper,
    checkoutData,
    cvbRefresh,
    paymentService
) {
    'use strict';

    const mixin = {

        /**
         * Update local storage with saved address data from CVB session
         * @param originFn
         * @returns {*}
         */
        resolveShippingAddress: function (originFn) {
            if (cvbRefresh.hasCvbCheckoutData()) {
                if (window.checkoutConfig.shippingAddressFromData) {
                    checkoutData.setShippingAddressFromData(window.checkoutConfig.shippingAddressFromData);
                }
            }

            return originFn()
        },

        /**
         * Update local storage with saved address data from CVB session
         * @param originFn
         * @returns {*}
         */
        resolveBillingAddress: function (originFn) {
            if (cvbRefresh.hasCvbCheckoutData()) {
                if (window.checkoutConfig.billingAddressFromData) {
                    checkoutData.setSelectedBillingAddress('new-customer-billing-address')
                    checkoutData.setNewCustomerBillingAddress(window.checkoutConfig.billingAddressFromData);
                }
            }

            return originFn()
        },

        /**
         * Preselect cvb payment method when a fill in session is used and the method is available (which should be always)
         * @param originFn
         * @returns {*}
         */
        resolvePaymentMethod: function (originFn) {
            const availablePaymentMethods = paymentService.getAvailablePaymentMethods(),
                selectedPaymentMethod = checkoutData.getSelectedPaymentMethod(),
                isBnplSelected = window.checkoutConfig.cvb.isBnplSelected,
                cvbAvailable = window.checkoutConfig.cvb.available;

            if (!cvbAvailable || !isBnplSelected || selectedPaymentMethod) {
                return originFn();
            }

            const cvbInList = availablePaymentMethods.some(function (paymentMethod) {
                return paymentMethod.method === 'cvb'
            })

            if (cvbInList) {
                // Actually preselect the method.
                checkoutData.setSelectedPaymentMethod('cvb')
            }

            return originFn();
        }
    };

    return function (target) {
        return wrapper.extend(target, mixin);
    };
});
