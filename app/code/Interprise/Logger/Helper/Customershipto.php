<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Interprise\Logger\Helper;

use \Interprise\Logger\Helper\Data;
use Magento\Framework\App\Helper\Context;

//use Magento\Setup\Exception;
use Magento\Framework\Validator\Exception;

/**
 * Description of Customershipto
 *
 * @author Shadab
 */
class Customershipto extends Data
{
    public $datahelper;

    public $storeManager;

    public $customerFactory;

    public $customer;

    public $connection;

    public $resource;

    public $defshippingadd;

    public $_iscustomer;

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
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Customer $customer,
        \Interprise\Logger\Helper\Customers $iscustomer
    ) {
        $this->storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->customer = $customer;
        $this->_iscustomer = $iscustomer;
        $this->defshippingadd = '';
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

    public function customershiptoSingle($datas)
    {
        $dataId = $datas['DataId'];
        $api_responsc = $this->getCurlData("customer?shipToCode=$dataId");
        $update_data['ActivityTime'] = $this->get_current_time();
        $update_data['Request'] = $api_responsc['request'];
        $response_api = '';
        if (isset($api_responsc['results']['data'])) {
            $response_api = $api_responsc['results']['data'];
        } else {
            $response_api = $api_responsc['results'];
        }
        $update_data['Response'] = json_encode($response_api);
        if (!$api_responsc['api_error']) {
            $update_data['Status'] = 'Fail';
            $update_data['Remarks'] = $api_responsc['results'];

            return $update_data;
        }
        $customer_data = $api_responsc['results']['data']['attributes'];
        $is_customer_code = $customer_data['customerCode'];
        //$default_shipto_code = $customer_data['defaultShipToCode'];
        $customer_existence = $this->checkCustomereByIscode($is_customer_code);
        if (!$customer_existence) {
            $update_data['Status'] = 'Fail';
            $update_data['Remarks'] = "Customer $is_customer_code not found for Shipto $dataId";

            return $update_data;
        }
        $magento_customer_id = $customer_existence;
        $addresses_interprise = $this->getCustomerAddress($is_customer_code);
        $ship_to_address = [];
        if (count($addresses_interprise['data']) > 0) {
            foreach ($addresses_interprise['data'] as $ks => $vs) {
                if ($vs['id'] == $dataId) {
                    $ship_to_address = $vs['attributes'];
                    break;
                }
            }
        }
        $shiptoname = $ship_to_address['shipToName'];
        $name_shipto_arr = $this->_iscustomer->makeCustomerNameWihCompany($shiptoname);
        $first_name = $name_shipto_arr['first_name'];
        $last_name = $name_shipto_arr['last_name'];
        if ($last_name == '') {
            $last_name = $first_name;
        }
        $company = $name_shipto_arr['company'];
        if (isset($ship_to_address['postalCode'])) {
            $postal_code = $ship_to_address['postalCode'];
        } else {
            $postal_code = 'NA';
        }

        $city = '';
        if (isset($ship_to_address['city'])) {
            $city = $ship_to_address['city'];
        } else {
            $city = "NA";
        }
        $region = '';
        if (isset($ship_to_address['county'])) {
            $region = $ship_to_address['county'];
        }

        $countrystate = '';
        if (isset($ship_to_address['state'])) {
            $countrystate = $ship_to_address['state'];
        }

        if (isset($ship_to_address['telephone']) && $ship_to_address['telephone'] != '') {
            $telephone = $ship_to_address['telephone'];
        } else {
            $telephone = 9999999999;
        }
        if (isset($ship_to_address['address'])) {
            $address = $ship_to_address['address'];
        } else {
            $address = 'NA';
        }

        $my_string = preg_replace(['/\r\n/'], '#PH', $address);
        $exploded = explode('#PH', $my_string);
        //$exploded = explode('\r\n', $data['address']);
        $full_address = '';
        $first_line = '';
        $second_line = '';

        $first_line = $exploded[0];

        if (isset($exploded[1]) && $exploded[1] != '') {
            $second_line = "\r\n" . trim($exploded[1]);
        }
        if (isset($exploded[2]) && $exploded[2] != '') {
            $second_line .= "," . trim($exploded[2]);
        }
        $address_for_magento = $first_line . $second_line;

        $country_name = $ship_to_address['country'];

        $country_code = $this->_iscustomer->getISOCodeCountryByName($country_name);

        $shipto_data = [];
        $shipto_data['interprise_shippingmethodgroup'] = $ship_to_address['shippingMethodGroup'];
        $shipto_data['interprise_shippingmethod'] = $ship_to_address['shippingMethod'];
        $shipto_data['interprise_freighttax'] = $ship_to_address['taxCode'];
        $shipto_data['shiptoCode'] = $ship_to_address['shipToCode'];
        $shipto_data['first_name'] = $first_name;
        $shipto_data['last_name'] = $last_name;
        $shipto_data['company'] = $company;
        $shipto_data['postalCode'] = $postal_code;
        $shipto_data['city'] = $city;
        $shipto_data['telephone'] = $telephone;
        $shipto_data['address'] = $address_for_magento;
        $shipto_data['country'] = $country_code;
        $shipto_data['region'] = $region;
        $shipto_data['state'] = $countrystate;
        $shipto_process = $this->_iscustomer->processShipto($shipto_data, $magento_customer_id);
        if ($shipto_process['Status']) {
            $update_data['Status'] = 'Success';
            $update_data['Remarks'] = "Success";

            return $update_data;
        } else {
            $update_data['Status'] = 'Fail';
            $update_data['Remarks'] = json_encode($shipto_process['error']);

            return $update_data;
        }
    }

    public function checkCustomereByIscode($customer_code)
    {
        $customercollection = $this->customerFactory->create()->getCollection()
                ->addAttributeToSelect("*")
                ->addAttributeToFilter("interprise_customer_code", ["eq" => $customer_code])
            - load();
        if ($customercollection->count() > 0) {
            $data = $customercollection->getFirstItem();

            return $data['entity_id'];
        } else {
            return 0;
        }
    }

    public function getCustomerAddress($customer_code)
    {
        $address_response = $this->getCurlData('customer/ShipTo?customerCode=' . $customer_code);

        return $address_response['results'];
    }
}
