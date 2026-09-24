<?php

namespace Fyb\Trade\Model\Quote\Address\Total;

use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use PHPUnit\Exception;

class TradePrice extends AbstractTotal
{

    private $checkoutSession;

    private $totalDedactedTax;

    private $totalNotDedactedTax;

    private $product;

    private $totalBaseDedactedTax;

    private $helper;

    private $customerRepository;

    public function __construct(
        Session $checkoutSession,
        \Fyb\Trade\Helper\Data $helper,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->totalDedactedTax = 0;
        $this->totalNotDedactedTax = 0;
        $this->helper = $helper;
        $this->customerRepository = $customerRepository;
    }

    public function collect(Quote $quote, ShippingAssignmentInterface $shippingAssignment, Quote\Address\Total $total)
    {
        parent::collect($quote, $shippingAssignment, $total);

        if (!$quote->getCustomerId() && !$this->helper->isTradeStore($quote->getStoreId())) {
            return $this;
        }

        try {
            $customer = $this->customerRepository->getById($quote->getCustomerId());
            $priceData = $this->helper->getCustomerData($customer);
            $discount = $priceData['discount'];

            foreach ($quote->getAllVisibleItems() as $item) {
                if ($discount) {
                    $price = $item->getProduct()->getFinalPrice();
                    $priceWithDiscount = ceil(($price - ($price * $discount)) * 100) / 100;

                    $item->setCustomPrice($priceWithDiscount);
                    $item->setOriginalCustomPrice($priceWithDiscount);
                    $item->getProduct()->setIsSuperMode(true);
                } else {
                    $item->setCustomPrice(null);
                    $item->setOriginalCustomPrice(null);
                }
            }
        } catch (Exception $e) {
        }

        return $this;
    }
}

