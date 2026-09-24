define([
    'mage/utils/wrapper',
    'jquery',
    'priceUtils',
    'Magento_Customer/js/customer-data',
], function (wrapper, $, priceUtils, customerData) {
    'use strict';

    var tradeCustomer = customerData.get('trade-customer');

    return function (initialObject) {
        initialObject.init = wrapper.wrapSuper(initialObject.init, function (options) {
            this._super(options);

            var discount = 0;
            if (tradeCustomer().priceType) {
                discount = tradeCustomer().discount;
            }

            $.each($('.price-final_price .price-wrapper'), function () {
                var elem = $(this);

                if (discount > 0) {
                    var price = elem.data('price-amount');
                    var formattedPrice = priceUtils.formatPrice(
                        Math.ceil((price - (price * discount)) * 100) / 100,
                        window.priceFormat,
                        false
                    );
                    elem.find('span').text(formattedPrice);
                }

                elem.show();
            });
        });

        return initialObject;
    };
});
