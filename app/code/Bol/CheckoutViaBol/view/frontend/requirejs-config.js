var config = {
    map: {
        '*': {
            'Magento_Checkout/js/view/form/element/email': 'Bol_CheckoutViaBol/js/view/checkout/form/element/email'
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/model/checkout-data-resolver': {
                'Bol_CheckoutViaBol/js/model/checkout-data-resolver-mixin': true
            }
        }
    }
};
