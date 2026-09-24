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
use \Interprise\Logger\Helper\Pushcustomer;

/**
 * Description of Pushcustomer
 *
 * @author geuser1
 */
class Pushcustomeraddress extends Data
{
    public $datahelper;

    public $storeManager;

    public $customerFactory;

    public $customer;

    public $connection;

    public $resource;

    public $_customerRepositoryInterface;

    public $_countryFactory;

    public $addressRepository;

    public $pushcustomer;

    public $addressfactory;

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
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Framework\App\ResourceConnection $resourceconnection,
        Pushcustomer $pushcustomer,
        \Magento\Customer\Model\AddressFactory $addressfactory
    ) {
        $this->storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->customer = $customer;
        $this->pushcustomer = $pushcustomer;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->addressfactory = $addressfactory;
        $this->addressRepository = $addressRepository;
        $this->_countryFactory = $countryFactory;
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

    public function pushcustomeraddressSingle($data)
    {
        $address_id = $data['DataId'];
        $update_data['ActivityTime'] = $this->get_current_time();
        $update_data['Request'] = 'To load customer by id';
        $update_data['Response'] = '';

        $address = $this->addressfactory->create();
        $address = $address->load($address_id);
        $customer_id = $address->getParentId();

        if (!$customer_id) {
            $method = __METHOD__;
            $update_data['Status'] = 'Fail';
            $update_data['Remarks'] = "Customer id not found for this address in function $method";

            return $update_data;
        }
        $customer_obj = $this->customerFactory->create()->load($customer_id);
        if (!$customer_obj->getData('interprise_customer_code')
            || $customer_obj->getData('interprise_customer_code') == '') {
            //if interprise customercode not present in magento then create new customer
            $customerarray['DataId'] = $customer_id;
            $this->pushcustomer->pushcustomerSingle($customerarray);
        }
        $iscustomercode = $customer_obj->getData('interprise_customer_code');
        $defaultBillingId = '';
        $defaultShippingId = '';
        if ($customer_obj->getDefaultBillingAddress()) {
            $defaultBillingId = $customer_obj->getDefaultBillingAddress()->getId();
        }
        if ($customer_obj->getDefaultShippingAddress()) {
            $defaultShippingId = $customer_obj->getDefaultShippingAddress()->getId();
        } else {
            $defaultShippingId = $defaultBillingId;
        }

        $default_array = ['default_billing_id' => $defaultBillingId, 'default_shipping_id' => $defaultShippingId];
        if ($address_id == $defaultBillingId) {
            $responce_data = $this->updateBillingAddress($address, $customer_obj);
            $update_data['Remarks'] = 'Billing address updated for customer ' . $iscustomercode;
        } else {
            if ($address->getData('interprise_shiptocode')) {
                $responce_data = $this->pushcustomer->editShippingAddress($address, $customer_obj, $default_array);
                $update_data['Remarks'] = 'Shipping address updated for customer ' . $iscustomercode;
            } else {
                $responce_data = $this->pushcustomer->addNewShippingAddress(
                    $address->getData(),
                    $customer_obj,
                    $default_array
                );
                $update_data['Remarks'] = 'Shipping address added for customer ' . $iscustomercode;
            }
        }
        if ($address_id == $defaultShippingId) {
            $this->updateDefaultShippingAddrass($address, $customer_obj);
            $update_data['Remarks'] = 'Default Shipping address updated for customer ' . $iscustomercode;
        }
        if ($responce_data['Status']) {
            $update_data['Status'] = 'Success';
            $update_data['JsonData'] = $responce_data['entity_id'];
        } else {
            $update_data['Status'] = 'Fail';
            $update_data['Remarks'] = json_encode($responce_data['error']);
            $update_data['JsonData'] = $responce_data['entity_id'];
        }

        return $update_data;
    }

    public function updateBillingAddress($address_object, $_customer_object)
    {
        $data['customerCode'] = $_customer_object->getData('interprise_customer_code');
        $defaultBillingData = $_customer_object->getDefaultBillingAddress();
        $arry_to_make_customer_name = [
            'firstname' => $defaultBillingData->getData('firstname'),
            'lastname' => $defaultBillingData->getData('lastname'),
            'company' => $defaultBillingData->getData('company'),
        ];
        $data['customerName'] = $this->pushcustomer->makeShiptoName($arry_to_make_customer_name);
        $data['telephone'] = $address_object->getData('telephone');
        $data['fax'] = $address_object->getData('fax');
        $contury_name = $this->pushcustomer->getCountryname($address_object->getData('country_id'));
        $countryclass_by_mapping = $this->pushcustomer->getCountryMappingForInterprise($contury_name);
        $country_name_mapped = $countryclass_by_mapping['interprise_country'];
        $classcode_mapped = $countryclass_by_mapping['interprise_customer_class'];
        $classcode_shipto_mapped = $countryclass_by_mapping['interprise_shipto_class'];
        $data['billingAddress'] = [
            'address' => str_replace("\n", "\r\n", $address_object->getData('street')),
            'city' => $address_object->getData('city'),
            'county' => $address_object->getData('region'),
            'postalCode' => $address_object->getData('postcode'),
            'telephone' => $address_object->getData('telephone'),
            'fax' => $address_object->getData('fax'),
            'country' => $country_name_mapped,
            'classcode' => $classcode_mapped,
        ];
        $result = $this->putCurlData('Customer', json_encode($data));
        if ($result['api_error']) {
            $result_array = json_decode($result['result'], true);
            if (is_array($result_array) && array_key_exists('errors', $result_array)) {
                $status['Status'] = false;
                $status['error'] = $result_array['errors'];
                $status['entity_id'] = json_encode($data);

                return $status;
            } else {
                $status['Status'] = true;
                $status['error'] = '';
                $status['entity_id'] = json_encode($data);

                return $status;
            }
        } else {
            $status['Status'] = false;
            $status['error'] = json_encode($result['error']);
            $status['entity_id'] = json_encode($data);

            return $status;
        }
    }

    public function updateDefaultShippingAddrass($address_object, $_customer_object)
    {
        $data['customerCode'] = $_customer_object->getData('interprise_customer_code');
        $data['customerName'] = $this->getCustomerName($_customer_object->getData('interprise_customer_code'));
        if ($address_object->getData('interprise_shiptocode')) {
            $data['defaultShipToCode'] = $address_object->getData('interprise_shiptocode');
            $result = $this->putCurlData('Customer', json_encode($data));
        }
    }

    public function getCustomerName($cust_id)
    {
        $customer_data = $this->getCurlData('customer?customercode=' . $cust_id);
        $data = $customer_data['results']['data']['attributes'];

        return $data['customerName'];
    }

    public function createAddressFromObj($address_object)
    {
        $data['telephone'] = $address_object->getData('telephone');
        $data['fax'] = $address_object->getData('fax');

        $contury_name = $this->pushcustomer->getCountryname($address_object->getData('country_id'));
        $countryclass_by_mapping = $this->pushcustomer->getCountryMappingForInterprise($contury_name);
        $country_name_mapped = $countryclass_by_mapping['interprise_country'];
        $classcode_mapped = $countryclass_by_mapping['interprise_customer_class'];
        $classcode_shipto_mapped = $countryclass_by_mapping['interprise_shipto_class'];
        $data['billingAddress'] = [
            'address' => str_replace("\n", "\r\n", $address_object->getData('street')),
            'city' => $address_object->getData('city'),
            'county' => $address_object->getData('region'),
            'postalCode' => $address_object->getData('postcode'),
            'telephone' => $address_object->getData('telephone'),
            'fax' => $address_object->getData('fax'),
            'country' => $country_name_mapped,
            'classcode' => $classcode_mapped,
        ];

        return $data;
    }
}
