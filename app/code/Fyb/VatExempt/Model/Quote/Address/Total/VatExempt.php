<?php

namespace Fyb\VatExempt\Model\Quote\Address\Total;

use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\Session as CatalogSession;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;

class VatExempt extends AbstractTotal
{
    private $catalogSession;

    private $checkoutSession;

    private $totalDedactedTax;

    private $totalNotDedactedTax;

    private $product;

    private $totalBaseDedactedTax;

    private $helper;

    private $customerRepository;

    public function __construct(
        Session $checkoutSession,
        ProductFactory $product,
        CatalogSession $catalogSession,
        \Meetanshi\VatExempt\Helper\Data $helper,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->catalogSession = $catalogSession;
        $this->totalDedactedTax = 0;
        $this->totalNotDedactedTax = 0;
        $this->product = $product;
        $this->helper = $helper;
        $this->customerRepository = $customerRepository;
    }

    public function collect(Quote $quote, ShippingAssignmentInterface $shippingAssignment, Quote\Address\Total $total)
    {
        parent::collect($quote, $shippingAssignment, $total);
        if (!$this->helper->isEnabled()) {
            return $this;
        }

        try {
            $products = explode(',', $this->catalogSession->getExemptProduct() ?? "");
            $flag = false;
            $items = $quote->getAllItems();

            $totalTaxAmount = 0;
            $totalBaseTaxAmount = 0;

            $isVatProductAvailable = false;
            foreach ($items as $quoteItem) {
                if ($quoteItem->getId() && $quoteItem->getProductType() != "bundle") {
                    if ($quoteItem->getProductType() == "configurable") {
                        $product = $this->product->create()->loadByAttribute('sku', $quoteItem->getSku());
                    } else {
                        $product = $this->product->create()->load($quoteItem->getProductId());
                    }

                    if ($product && $product->getIsVatexempt()) {
                        $isVatProductAvailable = true;
                    }
                }
            }

            if (!$isVatProductAvailable) {
                $quote->setData('vat_exempt_customer', "");
                $quote->setData('vat_exempt_reason', "");
                $quote->setData('vat_exempt_processed', false);
            } else {
                if (!$quote->getData('vat_exempt_processed') && $quote->getCustomerId()) {
                    try {
                        $customer = $this->customerRepository->getById($quote->getCustomerId());
                        $vatCustomer = $customer->getCustomAttribute('vatexemptcustomer_c')?->getValue();
                        $vatReason = $customer->getCustomAttribute('vatexemptreason_c')?->getValue();
                        $vatDate = $customer->getCustomAttribute('vatexemptexpirydate_c')?->getValue();
                        if ($vatDate && time() < strtotime($vatDate)) {
                            $quote->setData('vat_exempt_customer', $vatCustomer);
                            $quote->setData('vat_exempt_reason', $vatReason);
                        }
                    } catch (\Exception $e) {
                    }

                    $quote->setData('vat_exempt_processed', true);
                }
            }

            $this->totalDedactedTax = 0;
            $this->totalBaseDedactedTax = 0;

            if ($quote->getData('vat_exempt_reason') != '') {
                foreach ($items as $item) {
                    if ($item->getId() and $item->getProductType() != "bundle") {
                        if ($item->getProductType() == "configurable") {
                            $product = $this->product->create()->loadByAttribute('sku', $item->getSku());
                        } else {
                            $product = $this->product->create()->load($item->getProductId());
                        }

                        $taxAmount = ($item->getTaxAmount() != 0) ? $item->getTaxAmount() : $item->getVatExempted();
                        $taxBaseAmount = ($item->getBaseTaxAmount() != 0) ? $item->getBaseTaxAmount() : $item->getVatExemptedBase();

                        $totalTaxAmount = (float)$totalTaxAmount + (float)$taxAmount;
                        $totalBaseTaxAmount = (float)$totalBaseTaxAmount + (float)$taxBaseAmount;
                        /*$totalTaxAmount += $item->getTaxAmount();
                        $totalBaseTaxAmount += $item->getBaseTaxAmount();*/

                        if ($product && $product->getIsVatexempt()) {
                            $this->totalDedactedTax = (float)$this->totalDedactedTax + (float)$taxAmount;
                            $this->totalBaseDedactedTax = (float)$this->totalBaseDedactedTax + (float)$taxBaseAmount;

                            $item->setVatExempted($taxAmount);
                            $item->setVatExemptedBase($taxBaseAmount);

                            $item->setBaseTaxAmount(0.00);
                            $item->setTaxAmount(0.00);

                            $flag = true;
                        } else {
                            $item->setVatExempted(0);
                            $item->setVatExemptedBase(0);
                        }

                        $item->save();
                    }
                }
            }

            if ($flag) {
                // $quote->setData('vat_exempt_customer', $this->catalogSession->getExemptName());
                // $quote->setData('vat_exempt_reason', $this->catalogSession->getExemptReason());
                $quote->save();

                if ($total->getTaxAmount() >= $this->totalDedactedTax) {
                    $total->setTaxAmount($total->getTaxAmount() - $this->totalDedactedTax);
                    $total->setBaseTaxAmount($total->getBaseTaxAmount() - $this->totalBaseDedactedTax);

                    if ($totalTaxAmount > 0) {
                        $total->setTotalAmount('tax',  $total->getTotalAmount('tax') - $this->totalDedactedTax);
                        $total->setBaseTotalAmount('tax', $total->getBaseTotalAmount('tax') - $this->totalBaseDedactedTax);

                        $total->setData("subtotal_incl_tax", $total->getData("subtotal_incl_tax") - $this->totalDedactedTax);
                        $total->setData("base_subtotal_total_incl_tax", $total->getData("base_subtotal_total_incl_tax") - $this->totalBaseDedactedTax);
                        $total->setData("base_subtotal_incl_tax", $total->getData("base_subtotal_incl_tax") - $this->totalBaseDedactedTax);
                    } else {
                        $total->setTotalAmount('tax', 0);
                        $total->setBaseTotalAmount('tax', 0);
                    }
                }
            }
        } catch (NoSuchEntityException $e) {
        } catch (LocalizedException $e) {
        }

        return $this;
    }
}
