define([
    'jquery',
    'Magento_Catalog/js/price-utils',
    'Magento_Customer/js/customer-data',
], function ($, utils, customerData) {
    'use strict';

    var widgetMixin = {
        _init: function initPriceBox() {
            var box = this.element;

            if (!box?.find('.not-change-final').length) {
                this.options.prices.finalPrice = this.options.prices.oldPrice;
                this.options.prices.basePrice = this.options.prices.baseOldPrice;
            }

            box.trigger('updatePrice');

            this.cache.displayPrices = utils.deepClone(this.options.prices);
        },
    };

    return function (targetWidget) {
        $.widget('mage.priceBox', targetWidget, widgetMixin);

        return $.mage.priceBox;
    };
});
