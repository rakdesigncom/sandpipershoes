<?php

namespace Fyb\Shipping\Plugin;

use Magento\Checkout\Api\Data\PaymentDetailsInterface;
use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Model\ShippingInformationManagement;
use Magento\Quote\Api\CartRepositoryInterface;

class SetQuoteDropship
{
    /**
     * @var CartRepositoryInterface
     */
    protected CartRepositoryInterface $quoteRepository;

    public function __construct(
        CartRepositoryInterface $quoteRepository,
    ) {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param ShippingInformationManagement $subject
     * @param int $cartId
     * @param ShippingInformationInterface $addressInformation
     *
     * @return array
     */
    public function beforeSaveAddressInformation(ShippingInformationManagement $subject, $cartId, ShippingInformationInterface $addressInformation): array
    {
        $quote = $this->quoteRepository->getActive($cartId);
        $address = $addressInformation->getShippingAddress();

        if ($address->getExtensionAttributes() && $address->getExtensionAttributes()->getIsDropship()) {
            $quote->setData('is_dropship', $address->getExtensionAttributes()->getIsDropship());
        } else {
            $quote->setData('is_dropship', 0);
        }

        return [$cartId, $addressInformation];
    }
}
