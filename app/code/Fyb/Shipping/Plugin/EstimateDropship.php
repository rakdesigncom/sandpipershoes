<?php

namespace Fyb\Shipping\Plugin;

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\EstimateAddressInterface;
use Magento\Quote\Model\ShippingMethodManagement;

class EstimateDropship
{
    /**
     * Quote repository model
     *
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    public function __construct(CartRepositoryInterface $quoteRepository)
    {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param ShippingMethodManagement $subject
     * @param int $cartId
     * @param \Magento\Quote\Api\Data\EstimateAddressInterface $address
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeEstimateByAddress(ShippingMethodManagement $subject, $cartId, EstimateAddressInterface $address): array
    {
        $quote = $this->quoteRepository->getActive($cartId);
        if ($address->getExtensionAttributes() && $address->getExtensionAttributes()->getIsDropship()) {
            $quote->setData('is_dropship', $address->getExtensionAttributes()->getIsDropship());
        } else {
            $quote->setData('is_dropship', 0);
        }

        return [$cartId, $address];
    }

    /**
     * @param \Magento\Quote\Model\ShippingMethodManagement $subject
     * @param int $cartId
     * @param \Magento\Quote\Api\Data\AddressInterface $address
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeEstimateByExtendedAddress(ShippingMethodManagement $subject, $cartId, AddressInterface $address): array
    {
        $quote = $this->quoteRepository->getActive($cartId);
        if ($address->getExtensionAttributes() && $address->getExtensionAttributes()->getIsDropship()) {
            $quote->setData('is_dropship', $address->getExtensionAttributes()->getIsDropship());
        } else {
            $quote->setData('is_dropship', 0);
        }

        return [$cartId, $address];
    }
}
