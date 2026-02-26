var config = {
    config: {
        mixins: {
            'Amasty_CheckoutCore/js/view/onepage': {
                'MgtWizards_Base/js/view/onepage-mixin': true
            },
            'Magento_Checkout/js/checkout-loader': {
                'MgtWizards_Base/js/checkout-loader-mixin': true
            },
            'Magento_Checkout/js/view/summary/cart-items': {
                'MgtWizards_Base/js/view/summary/cart-items-mixin': true
            }
        }
    }
};