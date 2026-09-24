<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Interprise\Logger\Helper;

use \Interprise\Logger\Helper\Data;
use \Magento\Sales\Model\Order;
use Magento\Framework\App\Helper\Context;
use Magento\Setup\Exception;

/**
 * Description of Salesorderworkflow
 *
 * @author geuser1
 */
class Salesorderworkflow extends Data
{

    public $order;

    public $objectManager;

    public $resource;

    public $connection;

    public $salesoder_helper;

    protected $transationmaster;

    protected $transationdetail;

    protected $_shipmentRepository;

    protected $_trackFactory;

    protected $_shipmentNotifier;

    protected $state;

    protected $orderFactory;

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
        \Magento\Sales\Model\Order $_order,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Interprise\Logger\Helper\Salesorder $interprise_salesorder,
        \Interprise\Logger\Model\TransactionmasterFactory $transactionmasterFactory,
        \Interprise\Logger\Model\TransactiondetailFactory $transactiondetailFactory,
        \Magento\Sales\Model\Convert\Order $shipmentRepository,
        \Magento\Sales\Model\Order\Shipment\TrackFactory $trackFactory,
        \Magento\Shipping\Model\ShipmentNotifier $shipmentNotifier,
        \Magento\Framework\App\State $appstate
    ) {
        $this->order = $_order;
        $this->orderFactory = $orderFactory;
        $this->salesoder_helper = $interprise_salesorder;
        $this->transationmaster = $transactionmasterFactory;
        $this->transationdetail = $transactiondetailFactory;
        $this->_shipmentRepository = $shipmentRepository;
        $this->_trackFactory = $trackFactory;
        $this->_shipmentNotifier = $shipmentNotifier;
        $this->state = $appstate;
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

    public function salesorderworkflowSingle($datas)
    {
        //ini_set("display_errors","1");
        $update_data = [];
        echo '<br/>' . $dataId = $datas['DataId'];
        echo '<br/>' . $poCode = $this->getOrderPoCode($dataId);

        print_R([$dataId, $poCode]);
        $update_data['ActivityTime'] = $this->getCurrentTime();
        $update_data['Request'] = 'To check poCode of order';
        $update_data['Response'] = $poCode;
        if (!$poCode) {
            $update_data['Status'] = 'fail';
            $update_data['Remarks'] = "poCode not found on Interprise";

            return $update_data;
        }

        $api_responsc = $this->getCurlData('salesorder/' . $dataId . '/workflow');

        $update_data['Request'] = $api_responsc['request'];
        $update_data['Response'] = json_encode($api_responsc['results']['data']);
        if (!$api_responsc['api_error']) {
            $update_data['Status'] = 'fail';
            $update_data['Remarks'] = $api_responsc['results'];

            return $update_data;
        }
        $encoded_response = json_encode($api_responsc);
        $newStatus = $api_responsc['results']['data'][0]['attributes']['stage'];

        $time = $this->getCurrentTime();

        $statuss = $api_responsc['results']['data']['0']['attributes']['stage'];
        //$statuss = 'completed';
        //$state_status_array = $this->getStateStatus($statuss);
        $statuss = strtolower($statuss);

        echo '<br/>$statuss' . $statuss;

        print_r([$poCode]);
        echo '<br/>' . $incrementID = $this->getIncrementIDFromPoCode($poCode);
        if (!$incrementID) {
            $update_data['Status'] = 'fail';
            $update_data['Remarks'] = "IncrementID not found on Interprise";

            return $update_data;
        }
        $orderInfo = $this->order->loadByIncrementId($incrementID);
        echo '<br/>' . $orderId = $orderInfo->getId();
        $magSoNumber = $orderInfo->getSoNumber();
        if ($dataId != $magSoNumber) {
            $error_message = 'So number not matched with order ' . $incrementID;
            $update_data['Status'] = 'fail';
            $update_data['Remarks'] = $error_message;

            return $update_data;
        }
        //$orderId = $this->salesoder_helper->soOrderExist($dataId);
        if (!$orderId) {
            $error_message = 'Order ID not found in magento for Increment ID ' . $incrementID;
            $update_data['Status'] = 'fail';
            $update_data['Remarks'] = $error_message;

            return $update_data;
        }

        switch ($statuss) {
            case 'na':
            case 'approve credit':
                $orderState = Order::STATE_PENDING_PAYMENT;
                $orderStatus = Order::STATE_PENDING_PAYMENT;
                break;
            case 'voided':
            case 'void':
                $orderState = Order::STATE_CANCELED;
                $orderStatus = Order::STATE_CANCELED;
                break;
            case 'ready to post':
            case 'completed':
            case 'dispatched':
            case 'despatch':
                echo '<br/>$orderState ' . $orderState = Order::STATE_COMPLETE;
                echo '<br/>$orderStatus ' . $orderStatus = Order::STATE_COMPLETE;
                $courier = "Courier Name";
                $title = "Title";
                $t_no = "TST101";
                $shipmentResult = $this->createShipment($orderId, $courier, $title, $t_no);
                if ($shipmentResult == 0) {
                    $update_data['ActivityTime'] = $this->getcurrenttime();
                    $update_data['Response'] = 'fail';
                    $update_data['Status'] = 'fail';
                    $update_data['Remarks'] = "Can not update data until we get SO number:$orderId in our Order table";

                    return $update_data;
                } else if ($shipmentResult == 2) {
                    $update_data['ActivityTime'] = $this->getcurrenttime();
                    $update_data['Response'] = 'fail';
                    $update_data['Status'] = 'fail';
                    $update_data['Remarks'] = "Shipment can't be created.";

                    return $update_data;
                }

                break;
            case 'print pick note':
            case 'ready to invoice':
                $orderState = Order::STATE_PROCESSING;
                $orderStatus = Order::STATE_PROCESSING;
                break;
            default:
                $orderState = Order::STATE_NEW;
                $orderStatus = Order::STATE_NEW;
                break;
        }

        //$orderId = 97;
        $order = $this->orderFactory->create()->load($orderId);
        echo '<br/>$orderState' . $orderState = $orderState;
        try {
//            if ($orderState == Order::STATE_CANCELED && $order->getPayment() && $order->canCancel()) {
//                $order->cancel();
//            }
            $order->addCommentToStatusHistory('Order was set to "' . $orderState . '" by IP status "' . $statuss . '".', true);
            $order->setState($orderState)->setStatus($orderStatus);

            // if($statuss=='completed' || $statuss=='dispatched'){
            //     $history = $order->addStatusHistoryComment('Order was set to Complete by our automation tool.', true);
            //     $history->setIsCustomerNotified(true);
            // }

            $order->setIsCustomerNotified(true);
            $order->save();
            //echo '<br/>After Save';
            $update_data['Status'] = 'Success';
            $update_data['Remarks'] = 'Success';

            return $update_data;
        } catch (Exception $ex) {
            $err_message = $ex->getMessage();
            $update_data['Status'] = 'fail';
            $update_data['Remarks'] = 'In method ' . __METHOD__ . ' ' . $err_message;

            return $update_data;
        }
    }

    public function getOrderPoCode($dataId = '')
    {
        if ($dataId != '') {
            $api_responsc = $this->getCurlData('salesorder/' . $dataId);
            if (!$api_responsc['api_error']) {
                return false;
            } else {
                if (isset($api_responsc['results']['data']['attributes']['poCode']) &&
                    strpos($api_responsc['results']['data']['attributes']['poCode'], 'SPS') !== false
                ) {
                    $poCode = $api_responsc['results']['data']['attributes']['poCode'];
                    if($poCode != '' && $poCode){
                        return $poCode;
                    }
                }

                //$encoded_response = json_encode($api_responsc);
                if (isset($api_responsc['results']['data']['attributes']['salesRepOrderCode'])) {
                    $poCode = $api_responsc['results']['data']['attributes']['salesRepOrderCode'];
                    if ($poCode != '') {
                        return $poCode;
                    }
                }

                return false;
            }
        }
    }

    public function getIncrementIDFromPoCode($poCode = '')
    {
        $poCodeArr = explode("#", $poCode);
        if (isset($poCodeArr[1])) {
            return $poCodeArr[1];
        }

        return false;
    }

    public function createShipment($orderID, $courier, $title, $t_no)
    {
        // $data= array(
        // 'carrier_code' => $courier,
        // 'title' => $title,
        // 'number' => $t_no,
        // );
        if ($orderID != 0) {
            $collection_del = $this->orderFactory->create()->load($orderID);
            $convertOrder = $this->_shipmentRepository;
            $shipment = $convertOrder->toShipment($collection_del);
            foreach ($collection_del->getAllItems() as $orderItem) {
                if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                    continue;
                }
                $qtyShipped = $orderItem->getQtyToShip();
                $shipmentItem = $convertOrder->itemToShipmentItem($orderItem)->setQty($qtyShipped);
                $shipment->addItem($shipmentItem);
                $shipment->register();
            }
            $shipment->getOrder()->setIsInProcess(true);
            try {
                $shipment->save();
            } catch (\Exception $e) {
                return 2;
            } catch (\LocalizedException $ex) {
                return 2;
            }
            $shipment->getOrder()->save();
            //$track = $this->_trackFactory->create()->addData($data);
            //$shipment->addTrack($track)->save();
            $this->state->emulateAreaCode(
                \Magento\Framework\App\Area::AREA_FRONTEND,
                [$this->_shipmentNotifier, "notify"],
                [$shipment]
            );

            return 1;
        } else {
            return 0;
        }
    }

    public function notifyCustomer($shipment)
    {
        $this->_shipmentNotifier->notify($shipment);
    }
}
