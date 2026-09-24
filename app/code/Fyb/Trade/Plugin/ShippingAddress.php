<?php

namespace Fyb\Trade\Plugin;

use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\ShippingAddressManagement;

class ShippingAddress
{
    protected $customAttributes = [
        'is_dropship'
    ];

    /**
     * @param ShippingAddressManagement $subject
     * @param int $cartId
     * @param AddressInterface $address
     *
     * @return array
     */
    public function beforeAssign(ShippingAddressManagement $subject, $cartId, AddressInterface $address): array
    {
        $extensionAttributes = $address->getExtensionAttributes();
        if ($extensionAttributes) {
            foreach ($this->customAttributes as $attribute) {
                $methodName = str_replace(' ', '', ucwords(str_replace('_', ' ', $attribute)));
                $set = 'set' . $methodName;
                $get = 'get' . $methodName;

                $address->$set($extensionAttributes->$get());
            }
        }

        return [$cartId, $address];
    }
}
