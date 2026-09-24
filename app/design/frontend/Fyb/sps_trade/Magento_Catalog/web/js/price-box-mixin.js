define([
    'jquery',
    'Magento_Customer/js/customer-data',
], function ($, customerData) {
    'use strict';

    var widgetMixin = {
        updateRetailPrice: function () {
            var tradeCustomer = customerData.get('trade-customer');

            if (!tradeCustomer().priceType) {
                return;
            }
            var discount = tradeCustomer().discount;
            var $widget = this;

            $.each($widget.options.prices, function (key, item) {
                $widget.options.prices[key].amount =
                    Math.ceil((item.amount - (item.amount * discount)) * 100) / 100;
            });
        },

        _setDefaultsFromDataSet: function _setDefaultsFromDataSet() {
            this._super();

            this.updateRetailPrice();
        },
    };

    return function (targetWidget) {
        $.widget('mage.priceBox', targetWidget, widgetMixin);

        return $.mage.priceBox;
    };
});
