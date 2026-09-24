<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Interprise\Logger\Helper;

use \Interprise\Logger\Helper\Data;
use Magento\Framework\App\Helper\Context;
use Magento\Setup\Exception;

/**
 * Description of Invoice
 *
 * @author geuser1
 */
class Invoice extends Data
{
    public $_orderRepository;

    public $_invoiceService;

    public $_transaction;

    public $salesoder_helper;

    public $objectManager;

    public $resource;

    public $connection;

    public $_convertOrder;

    public $trackingFactory;

    protected $transationmaster;

    protected $transationdetail;

    protected $invoicecollection;

    protected $customerFactory;

    protected $statementFactory;

    protected $statementaccountFactory;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Catalog\Model\ProductFactory $product,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categorycollection,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryobj,
        \Interprise\Logger\Model\PricingcustomerFactory $pricingcustomer,
        \Interprise\Logger\Model\PricelistsFactory $pricelistsFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Customer\Model\Session $session,
        \Interprise\Logger\Model\CountryclassmappingFactory $classmapping,
        \Interprise\Logger\Model\StatementaccountFactory $statementaccountFactory,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Interprise\Logger\Model\CustompaymentFactory $custompaymentFactory,
        \Interprise\Logger\Model\CustompaymentitemFactory $custompaymentitemFactory,
        \Interprise\Logger\Model\PaymentmethodFactory $paymentmethodfact,
        \Interprise\Logger\Model\ResourceModel\Installwizard\CollectionFactory $installwizardFactory,
        \Interprise\Logger\Model\ShippingstoreinterpriseFactory $shippingstoreinterpriseFactory,
        \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\Transaction $transaction,
        \Interprise\Logger\Helper\Salesorder $interprise_salesorder,
        \Magento\Sales\Model\Convert\Order $convertOrder,
        \Magento\Sales\Model\Order\Shipment\TrackFactory $trackfactory,
        \Interprise\Logger\Model\TransactionmasterFactory $transactionmasterFactory,
        \Interprise\Logger\Model\TransactiondetailFactory $transactiondetailFactory,
        \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoicecolleciton,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Interprise\Logger\Model\ResourceModel\Statementaccount\CollectionFactory $statemnetcol
    ) {
        $this->_orderRepository = $orderRepository;
        $this->_invoiceService = $invoiceService;
        $this->_transaction = $transaction;
        $this->salesoder_helper = $interprise_salesorder;
        $this->_convertOrder = $convertOrder;
        $this->trackingFactory = $trackfactory;
        $this->transationmaster = $transactionmasterFactory;
        $this->transationdetail = $transactiondetailFactory;
        $this->invoicecollection = $invoicecolleciton;
        $this->customerFactory = $customerFactory;
        $this->statementFactory = $statemnetcol;
        $this->statementaccountFactory = $statementaccountFactory;
        parent::__construct(
            $context,
            $httpContext,
            $product,
            $curl,
            $datetime,
            $categorycollection,
            $productCollectionFactory,
            $categoryobj,
            $pricingcustomer,
            $pricelistsFactory,
            $productFactory,
            $session,
            $classmapping,
            $statementaccountFactory,
            $addressFactory,
            $custompaymentFactory,
            $custompaymentitemFactory,
            $paymentmethodfact,
            $installwizardFactory,
            $shippingstoreinterpriseFactory,
            $curlFactory
        );
    }

    public function invoiceSingle($datas)
    {
        $dataId = $datas['DataId'];
        $update_data = [];
        if (strpos($datas['JsonData'], '"type":"Invoice"') !== false
            || strpos($datas['JsonData'], '"type":"Invoice Write Off"') !== false
            || strpos($datas['JsonData'], '"type":"Credit Memo"') !== false
        ) {
            $api_responsc['api_error'] = 1;
            $api_responsc['status'] = 1;
            $api_responsc['results']['data']['type'] = 'customersalesorder';
            $api_responsc['results']['data']['attributes'] = json_decode($datas['JsonData'], true);
            $req = json_decode($datas['Request'], true);
            $api_responsc['request'] = $req[0];
        } else {
            $api_responsc = $this->getCurlData('salesorder/' . $dataId);
        }
        $api_responsc = $this->getCurlData('Invoice/' . $dataId);
        $update_data['ActivityTime'] = $this->getCurrentTime();
        $update_data['Request'] = $api_responsc['request'];
        $update_data['Response'] = isset($api_responsc['results']['data']) ? json_encode($api_responsc['results']['data']) : json_encode($api_responsc['results']);
        if (!$api_responsc['api_error']) {
            $update_data['Status'] = 'fail';
            $update_data['Remarks'] = $api_responsc['results'];

            return $update_data;
        }

        $checkSalesorder = explode("-", $dataId);
        $type = $checkSalesorder[0];
        $attribute_data = $api_responsc['results']['data']['attributes'];

        $customr_code_invoice = $attribute_data['billToCode'];
        $result_statement_update = $this->updateCustomerStatement($customr_code_invoice);
        if (!$result_statement_update['Status']) {
            $error_message_statement = $result_statement_update['error'];
            $remMessage = "Can not proceed invoice until statement of $customr_code_invoice not";
            $remMessage .= "popoulated and in population error occured: $error_message_statement";
            $update_data['Status'] = 'fail';
            $update_data['Remarks'] = $remMessage;

            return $update_data;
        }
        if ($type == 'INV' && !$attribute_data['isPosted']) {
            $update_data['Status'] = 'isPosted false';
            $update_data['Remarks'] = "Can not proceed which invoice isPosted is false";

            return $update_data;
        }
        $api_responsc_detail = $this->getCurlData('Invoice/' . $dataId . '/detail');
        if (!$api_responsc_detail['api_error']) {
            $api_responsc_detail = '';
        }
        $magento_customer_id = $this->salesoder_helper->isCustomerExistByInterpriseCode($customr_code_invoice);
        if (!$magento_customer_id) {
            $update_data['Status'] = 'fail';
            $update_data['Remarks'] = "Currently customer not exist in Magento :$customr_code_invoice ";

            return $update_data;
        }
        $so_exist = $this->salesoder_helper->checkDocumentCodeExistInTransaction($dataId);
        $time = $this->getCurrentTime();

        /////////////////// Code By Manisha On 05/12/2018 to get sales order details ////////////////////////

        $sourceSalesOrderCode = '';
        $rootDocumentCode = '';
        $poCode = '';
        $salesOrderDate = '';
        $dueDate = '';
        $shipToName = '';
        $paymentTermCode = '';
        $total = '';
        $balance = '';
        $isVoided = '';
        $orderStatus = '';
        $isPosted = '';
        $doc_type = '';

        if (!empty($attribute_data)) {
            $doc_type = $attribute_data['type'];
            $sourceSalesOrderCode = $attribute_data['sourceInvoiceCode'];
            $rootDocumentCode = $attribute_data['rootDocumentCode'];
            //$poCode               = $attribute_data['poCode'];
            //$salesOrderDate       = $attribute_data['salesOrderDate'];

            if (isset($attribute_data['dueDate'])) {
                $dueDate = $attribute_data['dueDate'];
            }
            //$dueDate              = $attribute_data['dueDate'];
            if (!isset($attribute_data['shipToName'])) {
                $shipToName = $attribute_data['billToName'];
            } else {
                $shipToName = $attribute_data['shipToName'];
            }
            if (isset($attribute_data['paymentTermCode'])) {
                $paymentTermCode = $attribute_data['paymentTermCode'];
            }

            $total = $attribute_data['total'];
            $balance = $attribute_data['balance'];
            $orderStatus = $attribute_data['orderStatus'];

            if ($attribute_data['isVoided']) {
                $isVoided = 1;
            } else {
                $isVoided = 0;
            }

            if ($attribute_data['isPosted']) {
                $isPosted = 1;
            } else {
                $isPosted = 0;
            }
        }
        /////////////////////////////////////////// End Code ////////////////////////////////////////////

        if ($so_exist) {
            $trmastermodel = $this->transationmaster->create()->load($so_exist);
            $trmastermodel->setData('updated_at', $time);
            $trmastermodel->setData('doc_type', $doc_type);
            $trmastermodel->save();
            $transdetailomod = $this->transationdetail->create()->load($so_exist);
            $transdetailomod->setData('json_data', json_encode($api_responsc));
            $transdetailomod->setData('json_detail', json_encode($api_responsc_detail));
            $transdetailomod->save();
            $update_data['Status'] = 'Success';
            $update_data['Remarks'] = 'Success';

            return $update_data;
        } else {
            $so_no = $attribute_data['sourceInvoiceCode'];
            /*if($so_no==''){
                $update_data['Status'] = 'fail';
                $update_data['Remarks'] ="SO number not found for this Invoice";
                return $update_data;
            }*/
            ///////////// Start commented by Shadab on 12052019  /////
            /* $get_order_no=$this->salesoder_helper->so_order_exist($so_no);
            if($get_order_no){
               $order_number=$get_order_no;
                $invoice_create=$this->create_invoice_magento($order_number, $api_responsc,$api_responsc_detail);
                if(!$invoice_create['Status']){
                    $update_data['Status']='fail';
                    $update_data['Remarks']=json_encode($invoice_create['error']);
                    return $update_data;
                }
            }

            $order_number=$get_order_no;*/
            ///////////// End commented by Shadab on 12052019  /////

        }
        //////////////////////////// end code //////////////////////////////////////

        $models_load = $this->transationmaster->create();
        $models_load->setData('doc_type', $doc_type);
        $models_load->setData('document_code', $dataId);
        $models_load->setData('updated_at', $time);
        $models_load->setData('customer_id', $customr_code_invoice);
        $models_load->setData('sourcesalesordercode', $sourceSalesOrderCode);
        $models_load->setData('rootdocumentcode', $rootDocumentCode);
        //$models_load->setData('pocode', $poCode);
        //$models_load->setData('salesorderdate', $salesOrderDate);
        $models_load->setData('duedate', $dueDate);
        $models_load->setData('shiptoname', $shipToName);
        $models_load->setData('paymenttermcode', $paymentTermCode);
        $models_load->setData('total', $total);
        $models_load->setData('balance', $balance);
        $models_load->setData('isvoided', $isVoided);
        $models_load->setData('status', $orderStatus);
        $models_load->save();
        if ($models_load->getId()) {
            $model_detail = $this->transationdetail->create();
            $model_detail->setData('json_data', json_encode($api_responsc));
            $model_detail->setData('document_code', $dataId);
            $model_detail->setData('json_detail', json_encode($api_responsc_detail));
            $model_detail->setData('customer_id', $magento_customer_id);
            $model_detail->save();
        }
        $update_data['Status'] = 'Success';
        $update_data['Remarks'] = 'Success';

        return $update_data;
    }

    public function updateCustomerStatement($customer_code)
    {
        ini_set("display_errors", "1");
        $tbl_name = 'interprise_statement_account';
        $magento_customer_id = $this->isCustomerExistByInterpriseCode($customer_code);
        if (!$magento_customer_id) {
            $msg = "$customer_code not exist in magento so
            unable to update customer account statemnt in table";
            $status['Status'] = false;
            $status['error'] = $msg;
            $status['entity_id'] = '';

            return $status;
        }
        $colfact = $this->statementFactory->create();
        $colfact->addFieldToFilter('customer_id', ['eq' => $magento_customer_id]);
        foreach ($colfact as $colobj) {
            $colobj->delete();
        }
        $api_responsc = $this->getCurlData("customer/statementofaccount?customercode=$customer_code");
        if (!$api_responsc['api_error']) {
            $status['Status'] = true;
            $status['error'] = json_encode($api_responsc);
            $status['entity_id'] = '';

            return $status;
        }
        $reference = '';
        $data = $api_responsc['results']['data'];
        foreach ($data as $key => $value) {
            $inser_data = [];
            $customer_id = $magento_customer_id;
            $invoice_code = $value['attributes']['invoiceCode'];
            $document_date = $value['attributes']['documentDate'];
            $due_date = $value['attributes']['dueDate'];
            $document_type = $value['attributes']['documentType'];
            $balance_rate = $value['attributes']['balanceRate'];
            $total_rate = $value['attributes']['totalRate'];
            if (isset($value['attributes']['reference'])) {
                $reference = $value['attributes']['reference'];
            }

            $statementfact = $this->statementaccountFactory->create();

            $statementfact->setData('customer_id', $customer_id);
            $statementfact->setData('invoice_code', $invoice_code);
            $statementfact->setData('document_date', $document_date);
            $statementfact->setData('due_date', $due_date);
            $statementfact->setData('document_type', $document_type);
            $statementfact->setData('balance_rate', $balance_rate);
            $statementfact->setData('total_rate', $total_rate);
            $statementfact->setData('reference', $reference);

            $statementfact->save();
        }
        $status['Status'] = true;
        $status['error'] = "";
        $status['entity_id'] = $magento_customer_id;

        return $status;
    }

    public function isCustomerExistByInterpriseCode($is_code)
    {
        $customercollection = $this->customerFactory->create()->getCollection()
            ->addAttributeToSelect("*")
            ->addAttributeToFilter("interprise_customer_code", ["eq" => $is_code])
            ->load();
        if ($customercollection->count() > 0) {
            $data = $customercollection->getFirstItem();

            return $data['entity_id'];
        } else {
            return 0;
        }
    }

    public function createInvoiceMagento($order_number, $api_responsc, $api_responsc_detail)
    {
        $orderId = $order_number; //order id for which want to create invoice
        $order = $this->_orderRepository->get($orderId);
        $attribute_data_in_creation = $api_responsc['results']['data']['attributes'];
        if (!$order->getId()) {
            $status['Status'] = false;
            $status['error'] = "Unable to load Magento order ID:$orderId ";
            $status['entity_id'] = '';

            return $status;
        }
        $invoice_code = $attribute_data_in_creation['invoiceCode'];
        $checkInMagento = $this->checkInvoiceExistInMagento($invoice_code);
        if ($checkInMagento) {
            $status['Status'] = false;
            $status['error'] = "Already this($invoice_code) invoice created in Magento";
            $status['entity_id'] = '';

            return $status;
        }
        if ($order->canInvoice()) {
            $attribute_itme_data = $api_responsc_detail['results']['data'];
            $is_item_info = [];
            if (count($attribute_itme_data) > 0) {
                foreach ($attribute_itme_data as $key => $value) {
                    $is_item_info[] = [
                        'sku' => $value['attributes']['itemName'],
                        'qty' => $value['attributes']['quantityAllocated'],
                    ];
                }
            }
            if (count($is_item_info) > 0) {
                $mage_itme_array = [];
                $orderItems = $order->getAllItems();
                if ($orderItems) {
                    foreach ($orderItems as $_itme) {
                        $mage_itme_array[$_itme->getSku()] = $_itme->getId();
                    }
                }
            }

            $itemsArray = [];
            if (count($is_item_info)) {
                foreach ($is_item_info as $key => $value) {
                    $itme_id = $this->assiginQty($value['sku'], $mage_itme_array);
                    if ($itme_id) {
                        $itemsArray[$itme_id] = $value['qty'];
                    }
                }
            }

            //$itemsArray = ['80'=>2]; //here 80 is order item id and 2 is it's quantity to be invoice

            //$shippingAmount = '10.00';
            $subTotal = $attribute_data_in_creation['subTotal'];
            $baseSubtotal = $attribute_data_in_creation['subTotalRate'];
            $grandTotal = $attribute_data_in_creation['total'];
            $baseGrandTotal = $attribute_data_in_creation['totalRate'];
            $invoice = $this->_invoiceService->prepareInvoice($order, $itemsArray);
            $invoice->setIsInvoice($invoice_code);
            $invoice->setSubtotal($subTotal);
            $invoice->setBaseSubtotal($baseSubtotal);
            $invoice->setGrandTotal($grandTotal);
            $invoice->setBaseGrandTotal($baseGrandTotal);
            $invoice->register();
            $transactionSave = $this->_transaction->addObject(
                $invoice
            )->addObject(
                $invoice->getOrder()
            );
            try {
                $transactionSave->save();
            } catch (Exception $ex) {
                $err_msg = $ex->getMessage();
                $status['Status'] = false;
                $status['error'] = "Err msg - " . $err_msg . " in function " . __METHOD__;
                $status['entity_id'] = '';

                return $status;
            }
            if (!$order->canShip()) {
                $status['Status'] = false;
                $status['error'] = "Invoice is created but failed to create shipment/Refused by Magento";
                $status['entity_id'] = '';

                return $status;
            }
            $orderShipment = $this->_convertOrder->toShipment($order);
            foreach ($order->getAllItems() as $orderItem) {
                // Check virtual item and item Quantity
                if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                    continue;
                }
                $result_qty = $itemsArray[$mage_itme_array[$orderItem->getSku()]];
                if (isset($result_qty) && $result_qty > 0) {
                    //$qty = $orderItem->getQtyToShip();
                    $qty = $result_qty;
                } else {
                    continue;
                }

                //$shipmentItem = $convertOrder->itemToShipmentItem($orderItem)->setQty($qty);
                $shipmentItem = $this->_convertOrder->itemToShipmentItem($orderItem)->setQty($qty);

                $orderShipment->addItem($shipmentItem);
            }
            //  $packaging['params'] = array(
//                        'container' => '',
//                        'weight' => $packageParams['weight'],
//                        'custom_value' => $subtotal,
//                        'length' => $packageParams['length'],
//                        'width' => $packageParams['width'],
//                        'height' => $packageParams['height'],
//                        'weight_units' => 'KILOGRAM',
//                        'dimension_units' => 'CENTIMETER',
//                        'content_type' => '',
//                        'content_type_other' => ''
//            );
//            $package[] = $packaging;
            //$orderShipment->setPackages($package);
            $orderShipment->register();
            $orderShipment->getOrder()->setIsInProcess(true);
            try {
                // Save created Order Shipment

                $orderShipment->save();
                $orderShipment->getOrder()->save();
                // $update_tracking_number = $this->setTrackingData();
                $status['Status'] = true;
                $status['error'] = "";
                $status['entity_id'] = $orderId;

                return $status;
                // Send Shipment Email
                //$this->_shipmentNotifier->notify($orderShipment);
                //$orderShipment->save();
            } catch (\Exception $e) {
                $err_msg = $e->getMessage();
                $status['Status'] = false;
                $status['error'] = "Err msg - " . $err_msg . " in function " . __METHOD__;
                $status['entity_id'] = '';

                return $status;
            }
            //$this->invoiceSender->send($invoice);
            //send notification code
            // $order->addStatusHistoryComment(
            //     __('Notified customer about invoice #%1.', $invoice->getId())
            // )
            //->setIsCustomerNotified(false)
            //->save();
        } else {
            $status['Status'] = false;
            $status['error'] = "Magento can not create Invoice/Refused by Magento";
            $status['entity_id'] = '';

            return $status;
        }
    }

    public function checkInvoiceExistInMagento($is_invoice_number)
    {
        $invoicecollection = $this->invoicecollection->create()->getCollection()
            ->addAttributeToSelect("*")
            ->addAttributeToFilter("is_invoice", ["eq" => $is_invoice_number])
            ->load();
        if ($invoicecollection->count() > 0) {
            $data = $invoicecollection->getFirstItem();

            return $data['entity_id'];
        } else {
            return 0;
        }
    }

    public function assiginQty($sku, $data_array)
    {
        if (array_key_exists($sku, $data_array)) {
            return $data_array[$sku];
        } else {
            false;
        }
    }

    public function setTrackingData($trackingNumber)
    {
        //$track = $this->trackingFactory->create();
        $track = $this->trackingFactory;
        $track->setTrackNumber($trackingNumber);
        //Carrier code can not be null/empty. Default carrier code is used
        $track->setCarrierCode('custom');//Put your carrier code here
        $track->setTitle('');//add your title here
        $trackInfo[] = $track;

        return $trackInfo;
    }
}
