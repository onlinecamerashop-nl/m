define([
    'jquery',

], function ($) {
    'use strict';

    return {
        hasCvbCheckoutData: function() {
            const query = new URLSearchParams(window.location.search);
            return query.has('cvb_ar') && query.get('cvb_ar') === '1'
        }
    };
});
