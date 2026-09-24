define([
    'jquery',
    'ko',
    'underscore',
    'uiRegistry',
    'Magento_Checkout/js/model/quote',
], function (
    $,
    ko,
    _,
    registry,
    quote,
) {
    'use strict';

    var mixin = {

        /**
         * on change billing address set `isAddressSameAsShipping` variable as a "false" for display Edit Button below the Address
         *
         * see /Magento_Checkout/view/frontend/web/template/billing-address/details.html template file
         */
        initObservable: function () {
            var result = this._super();

            quote.billingAddress.subscribe(function (newAddress) {
                this.isAddressSameAsShipping(false);
            }, this);

            return result;
        },

        /**
         * return false for hide the `My billing and shipping address are the same` checkbox
         *
         * see /Magento_Checkout/view/frontend/web/template/billing-address.html template file
         */
        canUseShippingAddress: ko.computed(function () {
            return false;
        }),

    }

    return function (target) {
        return target.extend(mixin);
    };
});
