<?php

namespace Fyb\Shipping\Plugin;

use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Model\Address\CustomerAddressDataFormatter;

class DisableAddressAttributes
{
    const SKIP_ATTRIBUTES = [
        'interprise_apicreatedaddress',
        'interprise_freighttax',
        'interprise_shippingmethod',
        'interprise_shippingmethodgroup',
        'interprise_shiptocode',
    ];

    /**
     * @param CustomerAddressDataFormatter $subject
     * @param array $result
     * @param AddressInterface $customerAddress
     *
     * @return array
     */
    public function afterPrepareAddress(CustomerAddressDataFormatter $subject, array $result, AddressInterface $customerAddress): array
    {
        if (isset($result['custom_attributes'])) {
            foreach (self::SKIP_ATTRIBUTES as $attribute) {
                if (isset($result['custom_attributes'][$attribute])) {
                    unset($result['custom_attributes'][$attribute]);
                }
            }
        }

        return $result;
    }
}
