/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiComponent',
    'Magento_Checkout/js/model/shipping-rates-validator',
    'Magento_Checkout/js/model/shipping-rates-validation-rules',
    '../../model/shipping-rates-validator/sps',
    '../../model/shipping-rates-validation-rules/sps'
], function (
    Component,
    defaultShippingRatesValidator,
    defaultShippingRatesValidationRules,
    dxShippingRatesValidator,
    dxShippingRatesValidationRules
) {
    'use strict';

    defaultShippingRatesValidator.registerValidator('sps', dxShippingRatesValidator);
    defaultShippingRatesValidationRules.registerRules('sps', dxShippingRatesValidationRules);

    return Component;
});
