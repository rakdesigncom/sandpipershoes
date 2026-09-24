/*jshint browser:true jquery:true*/
/*global alert*/
define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote'
], function ($, wrapper, quote) {
    'use strict';

    var skipAttributes = [];
    return function (setShippingInformationAction) {

        return wrapper.wrap(setShippingInformationAction, function (originalAction) {
            var shippingAddress = quote.shippingAddress();

            if (shippingAddress) {
                if (shippingAddress['extension_attributes'] === undefined) {
                    shippingAddress['extension_attributes'] = {};
                }

                if (shippingAddress.customAttributes !== undefined) {
                    $.each(shippingAddress.customAttributes, function (key, attribute) {
                        if (!skipAttributes.includes(attribute['attribute_code'])) {
                            var attrValue = (attribute['value'] === true || attribute['value'] === false) ? attribute['value'] * 1: attribute['value'];

                            shippingAddress['extension_attributes'][attribute['attribute_code']] = attrValue;
                        }
                    });
                }

                if (window.checkoutConfig?.storeCode === 'sps_trade') {
                    shippingAddress.saveInAddressBook = 0;
                }
            }

            return originalAction();
        });
    };
});
