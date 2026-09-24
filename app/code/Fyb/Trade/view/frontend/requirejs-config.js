var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/set-shipping-information': {
                'Fyb_Trade/js/action/set-shipping-information-mixin': true
            },
            'Magento_Checkout/js/action/select-shipping-address': {
                'Fyb_Trade/js/action/select-shipping-information-mixin': true
            },
            'Magento_Checkout/js/action/set-billing-address': {
                'Fyb_Trade/js/action/set-billing-address-mixin': true
            },
            'Magento_Checkout/js/action/place-order': {
                 'Fyb_Trade/js/action/set-billing-address-mixin': true
            },
        }
    }
};
