define(['jquery',
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
], function ($, ko, Component, quote) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Fyb_Trade/dropship_text'
        },

        initObservable: function () {
            this._super();
            this.tooltipText = ko.computed(function () {
                var shippingAddress = quote.shippingAddress();
                var attribute;

                if (shippingAddress) {
                    attribute = shippingAddress.customAttributes?.find(
                        function (element) {
                            return element.attribute_code === 'is_dropship';
                        }
                    );
                }

                return attribute?.value ? 'The order will be shipped direct to the customer': '';
            }, this);

            return this;
        },
    });
});
