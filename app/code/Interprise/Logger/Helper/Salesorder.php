<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Interprise\Logger\Helper;

use \Interprise\Logger\Helper\Data;
use Magento\Setup\Exception;

/**
 * Description of Salesorder
 *
 * @author geuser1
 */
class Salesorder extends Data
{
    public $customerFactory;
    public $order;
    public $_customer;
    public $_product;
    public $warehousecodefulfillment;
    public $objectManager;
    public $resource;
    public $connection;

    protected $_transactionmasterFactory;
    protected $_transactiondetailFactory;
    protected $_tdmodelFactory;
    protected $_trmacolFactory;
    protected $_ordercolectFactory;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Catalog\Model\ProductFactory $product,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categorycollection,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory  $productCollectionFactory,
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
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Interprise\Logger\Model\TransactionmasterFactory $transactionmasterFactory,
        \Interprise\Logger\Model\ResourceModel\Transactiondetail\CollectionFactory $transactiondetailFactory,
        \Interprise\Logger\Model\TransactiondetailFactory $tdmodelFactory,
        \Interprise\Logger\Model\ResourceModel\Transactionmaster\CollectionFactory $trmcolfact,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
    ) {
        $this->customerFactory  = $customerFactory;
        $this->order = $order;
        $this->_customer = $customer;
        $this->_product = $product;
        $this->_transactionmasterFactory = $transactionmasterFactory;
        $this->_transactiondetailFactory = $transactiondetailFactory;
        $this->_tdmodelFactory = $tdmodelFactory;
        $this->_trmacolFactory = $trmcolfact;
        $this->_ordercolectFactory = $orderCollectionFactory;
        $this->warehousecodefulfillment = "MAIN";
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
    public function salesorderSingle($datas)
    {

        $update_data=[];
        $dataId = $datas['DataId'];
        if (strpos($datas['JsonData'], '"type":"Sales Order"')!==false
            || strpos($datas['JsonData'], '"type":"Back Order"')!==false
            || strpos($datas['JsonData'], '"type":"Quote"')!==false) {
                $api_responsc['api_error']=1;
                $api_responsc['status']=1;
                $api_responsc['results']['data']['type']='customersalesorder';
                $api_responsc['results']['data']['attributes']=json_decode($datas['JsonData'], true);
                $req=json_decode($datas['Request'], true);
                $api_responsc['request']=$req[0];
        } else {
                $api_responsc = $this->getCurlData('salesorder/' . $dataId);

        }
            $update_data['ActivityTime'] = $this->getCurrentTime();
            $update_data['Request'] = $api_responsc['request'];
            $update_data['Response'] = json_encode($api_responsc['results']);
        if (!$api_responsc['api_error']) {
            $update_data['Status'] = 'fail';
            $update_data['Remarks'] = $api_responsc['results'];
            return $update_data;
        }
            $checkSalesorder = explode("-", $dataId);
        if (!in_array(strtolower($checkSalesorder[0]), ['so','bko','qu','crma'])) {
            $update_data['Status'] = 'fail';
            $update_data['Remarks'] = "Document not one of SO, BKO, QU or CRMA:$checkSalesorder ";
            return $update_data;
        }
            $order_data = $api_responsc['results']['data']['attributes'];
            $customer_code_interprise = $order_data['billToCode'];
            $magento_customer_id = $this->isCustomerExistByInterpriseCode($customer_code_interprise);

        if (!$magento_customer_id) {
            $update_data['Status'] = 'fail';
            $update_data['Remarks'] = "Customer does not exist. Import customer first :$customer_code_interprise ";
            return $update_data;
        }
            $so_exist=$this->checkDocumentCodeExistInTransaction($dataId);
             $time = $this->getCurrentTime();
             $api_responsc_detail = $this->getCurlData('salesorder/'.$dataId.'/detail');

        if (!$api_responsc_detail['api_error']) {
            $api_responsc_detail='';
        }

        if ($order_data['isVoided']) {
            $update_data['Status'] = 'Voided';
            $update_data['Remarks'] ="Cannot import voided document.";
            return $update_data;
        }
             /////////////////// Code By Manisha On 05/12/2018 to get sales order details ////////////////////////
             $sourceSalesOrderCode = '';
             $rootDocumentCode     = '';
             $poCode               = '';
             $salesOrderDate       = '';
             $dueDate              = '';
             $shipToName           = '';
             $paymentTermCode      = '';
             $total                = '';
             $balance              = '';
             $isVoided             = '';
             $orderStatus          = '';
             $doc_type             = '';
        if (!empty($order_data)) {

            $doc_type = $order_data['type'];

            $sourceSalesOrderCode = $order_data['sourceSalesOrderCode'];
            $rootDocumentCode     = $order_data['rootDocumentCode'];
            if (isset($order_data['poCode'])) {
                $poCode               = $order_data['poCode'];
            }
            $salesOrderDate = '';
            if (isset($order_data['salesOrderDate'])) {
                $salesOrderDate       = $order_data['salesOrderDate'];
            }

            if (isset($order_data['dueDate'])) {
                $dueDate              = $order_data['dueDate'];
            }

            if (isset($order_data['shipToName'])) {
                $shipToName           = $order_data['shipToName'];
            }
            $paymentTermCode      = $order_data['paymentTermCode'];
            $total                = $order_data['total'];
            $balance              = $order_data['balance'];
            $orderStatus          = $order_data['orderStatus'];
            if ($order_data['isVoided']) {
                $isVoided    = 1;
            } else {
                $isVoided    = 0;
            }
        }
             /////////////////////////////////////////// End Code ////////////////////////////////////////////
        if ($so_exist) {
            $moduels = $this->_transactionmasterFactory->create()->load($so_exist);
            $moduels->setData('updated_at', $time);
            $moduels->save();

            $collectioun =  $this->_transactiondetailFactory->create();
            $collectioun->addFieldToFilter('document_code', ['eq' => $dataId]);
            foreach ($collectioun as $colsobj) {
                $colsobj->setData('json_data', json_encode($api_responsc));
                $colsobj->setData('json_detail', json_encode($api_responsc_detail));
            }
            $collectioun->save();
        } else {
            $models_load = $this->_transactionmasterFactory->create();
            $models_load->setData('doc_type', $doc_type);
            $models_load->setData('document_code', $dataId);
            $models_load->setData('updated_at', $time);
            $models_load->setData('customer_id', $customer_code_interprise);
            $models_load->setData('sourcesalesordercode', $sourceSalesOrderCode);
            $models_load->setData('rootdocumentcode', $rootDocumentCode);
            $models_load->setData('pocode', $poCode);
            $models_load->setData('salesorderdate', $salesOrderDate);
            $models_load->setData('duedate', $dueDate);
            $models_load->setData('shiptoname', $shipToName);
            $models_load->setData('paymenttermcode', $paymentTermCode);
            $models_load->setData('total', $total);
            $models_load->setData('balance', $balance);
            $models_load->setData('isvoided', $isVoided);
            $models_load->setData('status', $orderStatus);
            $models_load->save();
            if ($models_load->getId()) {
                $model_detail = $this->_tdmodelFactory->create();
                $model_detail->setData('json_data', json_encode($api_responsc));
                $model_detail->setData('document_code', $dataId);
                $model_detail->setData('json_detail', json_encode($api_responsc_detail));
                $model_detail->setData('json_detail2', '');
                $model_detail->setData('customer_id', $magento_customer_id);
                $model_detail->save();
            }
        }
        $update_data['Status'] = 'Success';
        $update_data['Remarks'] = 'Success';
        return $update_data;
    }
    public function isCustomerExistByInterpriseCode($is_code)
    {
        $customercollection =$this->customerFactory->create()->getCollection()
                ->addAttributeToSelect("*")
                ->addAttributeToFilter("interprise_customer_code", ["eq" => $is_code])
            ->load();
        if ($customercollection->count()>0) {
            $data = $customercollection->getFirstItem();
            $data = $data->getData();
            return $data['entity_id'];
        } else {
            return false;
        }
    }
    public function checkDocumentCodeExistInTransaction($so_number)
    {
        $collec = $this->_trmacolFactory->create()
        ->addFieldToFilter("document_code", ["eq" => $so_number]);
        if ($collec->count()>0) {
            $data = $collec->getFirstItem();
            $data= $data->getData();
            return $data['id'];
        } else {
            return 0;
        }
    }
    public function soOrderExist($so_number)
    {
        $collec = $this->_ordercolectFactory->create()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('so_number', ["eq" => $so_number]); //Add condition if you wish
        //echo '<br/>'.$collec->count();
        if ($collec->count()>0) {
            $data = $collec->getFirstItem();
            //echo $data['entity_id'];
            return $data['entity_id'];
        } else {
            return 0;
        }
    }
}
