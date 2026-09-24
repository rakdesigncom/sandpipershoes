<?php

namespace Fyb\Theme\Plugin;

use Magento\Checkout\Block\Checkout\LayoutProcessor;

class CheckoutSortOrder
{
    protected $fieldsSortOrder = [
        'street' => 70,
        'city' => 71,
        'region_id' => 72,
        'postcode' => 73,
        'country_id' => 74,
    ];

    /**
     * @param LayoutProcessor $subject
     * @param array $result
     * @param array $jsLayout
     *
     * @return array
     */
    public function afterProcess(LayoutProcessor $subject, array $jsLayout): array
    {
//        print_r($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
//            ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']); exit;
        //Shipping Address
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']
        )) {
            $shippingFields = &$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']['children'];

            foreach ($shippingFields as $field => $configuration) {
                if (!isset($this->fieldsSortOrder[$field])) {
                    continue;
                }

                $shippingFields[$field]['sortOrder'] = $this->fieldsSortOrder[$field];
            }
        }

        //Billing Address on payment method
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
            ['children']['payment']['children']['payments-list']['children']
        )) {
            $paymentList = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
            ['children']['payment']['children']['payments-list']['children'];

            foreach($paymentList as $key => $payment) {
                //Exclude not billing forms
                if (!strpos($key, '-form')) {
                    continue;
                }

                foreach ($payment['children']['form-fields']['children'] as $field => $configuration) {
                    if (!isset($this->fieldsSortOrder[$field])) {
                        continue;
                    }

                    $paymentList[$key]['children']['form-fields']['children'][$field]['sortOrder'] = $this->fieldsSortOrder[$field];
                }
            }
        }

        //Billing Address on payment page
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children']['afterMethods']['children']['billing-address-form']['children']['form-fields']
            ['children']
        )) {
            $billingConfiguration = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children']['afterMethods']['children']['billing-address-form']['children']['form-fields']
            ['children'];

            foreach ($billingConfiguration as $field => $configuration) {
                if (!isset($this->fieldsSortOrder[$field])) {
                    continue;
                }

                $billingConfiguration[$field]['sortOrder'] = $this->fieldsSortOrder[$field];
            }
        }

        return $jsLayout;
    }
}
