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
 * Description of CustomerSpecialPrice
 *
 * @author geuser1
 */
class CustomerSpecialPrice extends Data
{
    public $datahelper;
    public $storeManager;
    public $customerFactory;
    public $connection;
    public $resource;
    public $pricingcollect;
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
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Interprise\Logger\Model\ResourceModel\Pricingcustomer\CollectionFactory $pricingcollection
    ) {
        $this->storeManager     = $storeManager;
        $this->customerFactory  = $customerFactory;
        $this->pricingcollect  = $pricingcollection;
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
    public function customerSpecialPriceSingle($datas)
    {

        $dataId = $datas['DataId'];
        $action = $datas['ActionType'];
        $update_data['ActivityTime'] = $this->get_current_time();
        $update_data['Request'] = 'To check customer exist in magento';
        $update_data['Response'] ='';
        $customer=$this->customerExistsByCustomercode($dataId);
        if (!$customer) {
            $remark_message = "Customer not found in Magento Store
            so we can not update the customers special price";
            $update_data['Response'] = "Customer not found in Magento Store";
            $update_data['Status'] = 'fail';
            $update_data['Remarks'] = $remark_message;
            return $update_data;
        }
        if ($action=='DELETE') {
            $result = $this->deleteSpecialPrice($dataId);
            $update_data['Status']='Success';
            $update_data['Remarks']='Success';
            return $update_data;
        }
        $itme_data=$this->getSpecialPricesCustomer($datas);
        if (!$itme_data['Status']) {
            $update_data['Status']='fail';
            $update_data['Remarks']=$itme_data['error'];
            return $update_data;
        }
        if (count($itme_data['entity_id'])>0) {
                    $interprise_table_name='interprise_pricing_customer';
                    $result = $this->deleteSpecialPrice($dataId);
            foreach ($itme_data['entity_id'] as $_item_code => $_item_data) {
                foreach ($_item_data as $ks => $vs) {
                    $itemcode=$_item_code;
                    $customer_code=$dataId;
                    $price=$vs['unitSellingPrice'];
                    $min_qty=$vs['min'];
                    $upto=$vs['max'];
                    $currency = 'GBP';
                    $prcustload = $this->pricingcustomerfact->create()->load();
                    $prcustload->setData('item_code', $itemcode);
                    $prcustload->setData('customer_code', $customer_code);
                    $prcustload->setData('price', $price);
                    $prcustload->setData('min_qty', $min_qty);
                    $prcustload->setData('qty_upto', $upto);
                    $prcustload->setData('currency', $currency);
                    $prcustload->save();
                }
            }
        }

                $update_data['Status']='Success';
            $update_data['Remarks']='Success';
        return $update_data;
    }
    public function customerExistsByCustomercode($customer_code)
    {
        $customercollection =$this->customerFactory->create()->getCollection()
                ->addAttributeToSelect("*")
                ->addAttributeToFilter("interprise_customer_code", ["eq" => $customer_code])
                -load();
        if ($customercollection->count()>0) {
            $data = $customercollection->getFirstItem();
            return $data['entity_id'];
        } else {
            return 0;
        }
    }
    public function deleteSpecialPrice($customer_code)
    {
        $collectioun = $this->pricingcollect->create();
        $collectioun->addFieldToFilter('customer_code', ['eq' => $customer_code]);
        foreach ($collectioun as $colsobj) {
            $colsobj->delete();
        }
    }
    public function getSpecialPricesCustomer($customer_data_array)
    {
         $customer_data_array_attribute= json_decode($customer_data_array['JsonData'], true);
         $updateDate=$customer_data_array_attribute['attributes']['dateCreated'];
        $customer_code=$customer_data_array['DataId'];
        $item_responce = $this->getCurlData('customer/SpecialPrice?customerCode='.$customer_code.'&date='.$updateDate);

        if (!$item_responce['api_error']) {
                $status['Status']=false;
                $status['error']= json_encode($item_responce['results']);
                $status['entity_id']='';
                return $status;
        }
        $result_data=$item_responce['results']['data'];
        $itme_array=[];
        if (count($result_data)) {
            foreach ($result_data as $key => $_item) {
                    $item_attribute=$_item['attributes'];
                    $itme_array[$item_attribute['itemCode']][]=$item_attribute;
            }
        }
        $status['Status']=true;
        $status['error']='';
        $status['entity_id']=$itme_array;
        return $status;
    }
}
