/*jshint browser:true jquery:true*/
/*global alert*/
define([
    'jquery',
    'mage/utils/wrapper',
    'uiRegistry',
    'Magento_Checkout/js/model/quote'
], function ($, wrapper, registry, quote) {
    'use strict';

    var skipAttributes = [];
    return function (selectShippingInformationAction) {

        return wrapper.wrap(selectShippingInformationAction, function (originalAction, shippingAddress) {
            if (shippingAddress) {
                if (shippingAddress['extensionAttributes'] === undefined) {
                    shippingAddress['extensionAttributes'] = {};
                }

                if (shippingAddress.customAttributes !== undefined) {
                    $.each(shippingAddress.customAttributes, function (key, attribute) {
                        if (!skipAttributes.includes(attribute['attribute_code'])) {
                            var attrValue = (attribute['value'] === true || attribute['value'] === false) ? attribute['value'] * 1 : attribute['value'];
                            shippingAddress['extensionAttributes'][attribute['attribute_code']] = attrValue;
                        }
                    });
                }

                if (window.checkoutConfig?.storeCode === 'sps_trade') {
                    shippingAddress.saveInAddressBook = 0;
                }
            }

            return originalAction(shippingAddress);
        });
    };
});
