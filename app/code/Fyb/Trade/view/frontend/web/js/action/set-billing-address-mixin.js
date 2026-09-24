define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote'
], function ($, wrapper, quote) {
    'use strict';

    var skipAttributes = [];
    return function (setBillingAddressAction) {
        return wrapper.wrap(setBillingAddressAction, function (originalAction, messageContainer) {

            var billingAddress = quote.billingAddress();
            if (billingAddress) {
                if (billingAddress['extension_attributes'] === undefined) {
                    billingAddress['extension_attributes'] = {};
                }

                if (billingAddress.customAttributes !== undefined) {
                    $.each(billingAddress.customAttributes, function (key, attribute) {
                        if (!skipAttributes.includes(attribute['attribute_code'])) {
                            var attrValue = (attribute['value'] === true || attribute['value'] === false) ? attribute['value'] * 1 : attribute['value'];
                            billingAddress['extension_attributes'][attribute['attribute_code']] = attrValue;
                        }
                    });
                }

                if (window.checkoutConfig?.storeCode === 'sps_trade' && billingAddress.getType() === 'new-customer-billing-address') {
                    billingAddress.saveInAddressBook = 0;
                }
            }

            return originalAction(messageContainer);
        });
    };
});
