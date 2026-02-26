define([
    'uiComponent',
], function (Component) {
    return Component.extend({
        async redirectToCvb() {
            const response = await fetch('/cvb/session/create');
            const json = await response.json();
            window.location.href = json['redirectUrl'];
        },

        fontUrl() {
            return require.toUrl('Bol_CheckoutViaBol/fonts/Graphik-Regular.ttf');
        },

        labelUrl() {
            return require.toUrl('Bol_CheckoutViaBol/images/button_checkout_via_bol.svg');
        },

        label() {
            return window.checkoutConfig.cvb.texts.title;
        },

        info() {
            return window.checkoutConfig.cvb.texts.description
        },

        isVisible: function () {
            if (!this.isCvbAvailable()) {
                return false;
            }

            return !window.checkoutConfig?.cvb?.hideCvbButtonInCheckout;
        },

        isCvbAvailable: function () {
            return window.checkoutConfig.hasOwnProperty('cvb')
                ? window.checkoutConfig.cvb.available
                : false
        },
    });
});
