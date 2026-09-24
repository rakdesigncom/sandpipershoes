<?php

namespace Fyb\VatExempt\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\Controller\Result\JsonFactory as ResultJsonFactory;
use Magento\Checkout\Model\Session;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Helper\Data;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Directory\Model\Currency;

class Index extends Action
{
    private $resultPageFactory;
    private $jsonHelper;
    private $quoteRepository;
    private $resultJsonFactory;
    private $checkoutSession;
    private $total;
    private $productFactory;
    private $product;
    private $taxHelper;
    private $totalDedactedTax;
    private $totalBaseDedactedTax;
    private $totalNotDedactedTax;
    protected $storeManager;
    protected $currency;
    public $currentCurrencyRate = 1;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CartRepositoryInterface $quoteRepository,
        ResultJsonFactory $resultJsonFactory,
        Session $checkoutSession,
        Total $total,
        ProductFactory $productFactory,
        ProductRepository $product,
        Data $taxHelper,
        JsonHelper $jsonHelper,
        StoreManagerInterface $storeManager,
        Currency $currency
    )
    {
        parent::__construct($context);
        $this->jsonHelper = $jsonHelper;
        $this->resultPageFactory = $resultPageFactory;
        $this->quoteRepository = $quoteRepository;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->checkoutSession = $checkoutSession;
        $this->productFactory = $productFactory;
        $this->product = $product;
        $this->total = $total;
        $this->totalDedactedTax = 0;
        $this->totalBaseDedactedTax = 0;
        $this->totalNotDedactedTax = 0;
        $this->taxHelper = $taxHelper;
        $this->storeManager = $storeManager;
        $this->currency = $currency;

        $this->currentCurrencyRate = $this->storeManager->getStore()->getCurrentCurrencyRate();

    }

    public function execute()
    {

        try {
            $data = $this->jsonHelper->jsonDecode($this->getRequest()->getContent());

            if ($data['apply']) {
                $quote = $this->checkoutSession->getQuote();
                $quote = $this->quoteRepository->get($quote->getEntityId());
//
                $items = $quote->getAllItems();

                foreach ($items as $item) {
                    if ($item->getId() and $item->getProductType() != "bundle")
                    {
                        if($item->getProductType() == "configurable")
                        {   $product = $this->product->get($item->getSku());    }
                        else
                        {   $product = $this->product->getById($item->getProductId());  }

                        if ($product->getIsVatexempt()) {

                            $taxAmount = ($item->getTaxAmount() != 0) ? $item->getTaxAmount() : $item->getVatExempted();
                            $taxBaseAmount = ($item->getBaseTaxAmount() != 0) ? $item->getBaseTaxAmount() : $item->getVatExemptedBase();

                            $this->totalDedactedTax = (float)$this->totalDedactedTax + (float)$taxAmount;

                            $this->totalBaseDedactedTax = (float)$this->totalBaseDedactedTax + (float)$taxBaseAmount;

                            $item->setVatExempted($taxAmount);
                            $item->setVatExemptedBase($taxBaseAmount);

                            $item->setTaxAmount(0);
                            $item->setBaseTaxAmount(0);

                        } else {
                            $item->setVatExempted(0);
                            $item->setVatExemptedBase(0);
                        }
                    }
                }
//
//                $shippingAddress = $quote->getShippingAddress();
//
//                $shippingAddress->setTaxAmount($shippingAddress->getTaxAmount() - $this->totalDedactedTax);
//                $shippingAddress->setBaseTaxAmount($shippingAddress->getBaseTaxAmount() - $this->totalBaseDedactedTax);
//
//                $shippingAddress->setSubtotalInclTax($shippingAddress->getSubtotalInclTax() - $this->totalDedactedTax);
//                $shippingAddress->setBaseSubtotalTotalInclTax($shippingAddress->getBaseSubtotalTotalInclTax() - $this->totalBaseDedactedTax);
//
//                $shippingAddress->setGrandTotal($shippingAddress->getGrandTotal() - $this->totalDedactedTax);
//                $shippingAddress->setBaseGrandTotal($shippingAddress->getBaseGrandTotal() - $this->totalBaseDedactedTax);
//
//                $quote->setGrandTotal($quote->getGrandTotal() - $this->totalDedactedTax);
//                $quote->setBaseGrandTotal($quote->getBaseGrandTotal() - $this->totalBaseDedactedTax);

                $quote->setData('vat_exempt_customer', $data['exemptname']);
                $quote->setData('vat_exempt_reason', $data['exemptreason']);

                $quote->getShippingAddress()->setCollectShippingRates(true);
                $quote->collectTotals();

                $quote->save();
                $response = [
                    'message' => 'Applied for VAT Exemption Successfully.'
                ];
            } else {

                $flag = false;
                $quote = $this->checkoutSession->getQuote();

                // $items = $quote->getAllVisibleItems();
                $items = $quote->getAllItems();

                foreach ($items as $item) {
                    if ($item->getId() && $item->getProductType() != "bundle")
                    {
//                        if($item->getProductType() == "configurable")
//                        {   $product = $this->product->get($item->getSku());    }
//                        else
//                        {   $product = $this->product->getById($item->getProductId());  }


//                        if ($product->getIsVatexempt()) {
//
//                            $taxAmount = ($item->getTaxAmount() != 0) ? $item->getTaxAmount() : $item->getVatExempted();
//                            $taxBaseAmount = ($item->getBaseTaxAmount() != 0) ? $item->getBaseTaxAmount() : $item->getVatExemptedBase();
//
//                            $this->totalDedactedTax = (float)$this->totalDedactedTax + (float)$taxAmount;
//                            $this->totalBaseDedactedTax = (float)$this->totalBaseDedactedTax + (float)$taxBaseAmount;
//
//                            $item->setBaseTaxAmount($taxBaseAmount);
//                            $item->setTaxAmount($taxAmount);

                            $item->setVatExempted(0);
                            $item->setVatExemptedBase(0);

                            $flag = true;

//                        }
                    }
                }

//                $shippingAddress = $quote->getShippingAddress();
//                $shippingAddress->setBaseTaxAmount($shippingAddress->getBaseTaxAmount() + $this->totalBaseDedactedTax);
//                $shippingAddress->setTaxAmount($shippingAddress->getTaxAmount() + $this->totalDedactedTax);
//
//                $shippingAddress->setBaseSubtotalTotalInclTax($shippingAddress->getBaseSubtotalTotalInclTax() + $this->totalBaseDedactedTax);
//                $shippingAddress->setSubtotalInclTax($shippingAddress->getSubtotalInclTax() + $this->totalDedactedTax);
//
//                $shippingAddress->setBaseGrandTotal($shippingAddress->getBaseGrandTotal() + $this->totalBaseDedactedTax);
//                $shippingAddress->setGrandTotal($shippingAddress->getGrandTotal() + $this->totalDedactedTax);
//
//                $quote->setBaseGrandTotal($quote->getBaseGrandTotal() + $this->totalBaseDedactedTax);
//                $quote->setGrandTotal($quote->getGrandTotal() + $this->totalDedactedTax);

                $quote->setData('vat_exempt_customer', '');
                $quote->setData('vat_exempt_reason', '');

                $quote->getShippingAddress()->setCollectShippingRates(true);
                $quote->collectTotals();

                $quote->save();
                $response = [
                    'message' => 'VAT Exemption Cancelled Successfully.'
                ];
            }
        } catch (\Exception $e) {
            $response = [
                'errors' => true,
                'message' => $e->getMessage()
            ];
        }

        return $this->resultJsonFactory->create()->setData($response);
    }
}

