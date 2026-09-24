define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'interprisepaid',
                component: 'Interprise_Logger/js/view/payment/method-renderer/interprisepaid-method'
            }
        );
        return Component.extend({});
    }
);