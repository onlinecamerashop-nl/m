define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';

        rendererList.push(
            {
                type: 'cvb',
                component: 'Bol_CheckoutViaBol/js/view/payment/method-renderer/cvb-method'
            }
        );

        return Component.extend({});
    }
);
