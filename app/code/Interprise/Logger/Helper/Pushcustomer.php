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
 * Description of Pushcustomer
 *
 * @author geuser1
 */
class Pushcustomer extends Data
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
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
    ) {
        $this->storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->customer = $customer;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
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

    public function pushcustomerSingle($data)
    {
        $customer_id = $data['DataId'];
        $isVatExempt = $data['isVatExempt'];
        $magento_customer_id = $customer_id;
        $json = '';
        $customer_obj = $this->customerFactory->create()->load($customer_id);
        if (!$customer_obj->getId()) {
            $update_data['Status'] = 'fail';
            $update_data['ActivityTime'] = $this->getCurrentTime();
            $update_data['Request'] = "NA";
            $update_data['Remarks'] = "Customer does not exist in Store";

            return $update_data;
        }
        $get_customer_detail = $customer_obj->toArray();
        $get_customer_address = $this->getCustomerAddressFromStore($customer_obj);
        if (isset($get_customer_detail['interprise_customer_code'])
            && $get_customer_detail['interprise_customer_code'] != '') {
            $interprise_id = $get_customer_detail['interprise_customer_code'];
        } else {
            $interprise_id = '';
        }
        //echo '<br/>interprise_id'.$interprise_id;

        if (isset($interprise_id) && $interprise_id != '') {
///If customer exists in interprise then update customer only
            $update_json_array = [
                'customerCode' => $interprise_id,
                //'customerName' => $is_customer_name,
                'email' => $get_customer_detail['email'],
            ];
            if ($isVatExempt) {
                if (isset($get_customer_detail['vatexemptexpirydate_c'])) {
                    $vatExpiryDate = $get_customer_detail['vatexemptexpirydate_c'];
                } else {
                    $currentDate = $this->getCurrentTime();
                    $vatExpiryDate = date('Y-m-d H:i:s', strtotime('+3 years', strtotime($currentDate)));
                }
                $customFields[] = ["field" => "VATExempt_C", "value" => true];
                $customFields[] = ["field" => "VATExemptExpiryDate_C", "value" => $vatExpiryDate];
                $update_json_array['customFields'] = $customFields;
            }

            try {
                $result_update_customer = $this->putCurlData(
                    'customer',
                    json_encode(
                        $update_json_array,
                        JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE
                    )
                );

                $update_data['entity_id'] = $interprise_id;

                //$decoded_update  = json_decode($result_update_customer['result'], true);
                if (is_array($result_update_customer) && $result_update_customer['api_error'] == 0) {
                    $update_data['Status'] = 'fail';
                    $update_data['Request'] = json_encode(
                        $update_json_array,
                        JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE
                    );
                    $update_data['Remarks'] = $result_update_customer['request'];
                    $update_data['ActivityTime'] = $this->getCurrentTime();
                    //return $update_data;
                } else {
                    $update_data['Status'] = 'Success';
                    $update_data['Remarks'] = "Customer " . $interprise_id . " updated";
                    $update_data['Response'] = $result_update_customer['result'];
                }

                return $update_data;
            } catch (Exception $ex) {
                $update_data['Status'] = 'Fail';
                $update_data['ActivityTime'] = $this->getCurrentTime();
                $update_data['Request'] = "$update_json_array";
                $update_data['Remarks'] = $ex->getMessage();

                return $update_data;
            } catch (\Magento\Framework\Validator\Exception $ex1) {
                $update_data['Status'] = 'Fail';
                $update_data['ActivityTime'] = $this->getCurrentTime();
                $update_data['Request'] = "$update_json_array";
                $update_data['Remarks'] = $ex1->getMessage() . " in function " . __METHOD__;

                return $update_data;
            }
        } else {
            // if new customer
            //if (count($get_customer_address) > 0) {
            $defaultBillingId = '';
            $defaultShippingId = '';

            // if ($customer_obj->getDefaultBillingAddress()) {
            //     $defaultBillingId = $customer_obj->getDefaultBillingAddress()->getId();
            //     $customer_obj->getDefaultBillingAddress()->setInterpriseShiptocode('billing');
            //     $customer_obj->getDefaultBillingAddress()->setIsDefaultBilling('1');
            //     $customer_obj->getDefaultBillingAddress()->save();
            // } elseif ($customer_obj->getDefaultShippingAddress()) {

            //     $defaultBillingId = $customer_obj->getDefaultShippingAddress()->getId();
            //     $customer_obj->getDefaultShippingAddress()->setInterpriseShiptocode('billing');
            //     $customer_obj->getDefaultShippingAddress()->setIsDefaultBilling('1');
            //     $customer_obj->getDefaultShippingAddress()->save();

            // } else {

            // $update_data['Status'] = 'fail';
            // $update_data['Remarks'] = "No default billing address found - cannot create customer";
            // $update_data['Request'] = "NA";
            // $update_data['Response'] = "NA";
            // $update_data['ActivityTime'] = $this->getCurrentTime();
            // return $update_data;
            //}
            //die;
            // if ($customer_obj->getDefaultShippingAddress()) {
            //     $defaultShippingId = $customer_obj->getDefaultShippingAddress()->getId();
            // } else {
            //     $defaultShippingId=$defaultBillingId;
            // }
            // $default_billing_address = $get_customer_address[$defaultBillingId];
            // $default_shipping_address = $get_customer_address[$defaultShippingId];

            if ($customer_obj->getDefaultBillingAddress()) {
                $defaultBillingId = $customer_obj->getDefaultBillingAddress()->getId();
                $customer_obj->getDefaultBillingAddress()->setInterpriseShiptocode('billing');
                $customer_obj->getDefaultBillingAddress()->setIsDefaultBilling('1');
                $customer_obj->getDefaultBillingAddress()->save();
                $default_billing_address = $get_customer_address[$defaultBillingId];
            } else {
                $billing_address = $data['billing_address_obj']->getData();

                $address_cleaned = $billing_address['street'];
                $default_billing_address = [
                    'street' => str_replace("\n", ", ", $address_cleaned),
                    'city' => $billing_address['city'],
                    'region' => $billing_address['region'],
                    'postcode' => strtoupper($billing_address['postcode']),
                    'telephone' => $billing_address['telephone'],
                    'firstname' => $billing_address['firstname'],
                    'lastname' => $billing_address['lastname'],
                    'company' => $billing_address['company'],
                    'country_id' => $billing_address['country_id'],

                ];
            }

            if ($customer_obj->getDefaultShippingAddress()) {
                $defaultShippingId = $customer_obj->getDefaultShippingAddress()->getId();
                $customer_obj->getDefaultShippingAddress()->setInterpriseShiptocode('billing');
                $customer_obj->getDefaultShippingAddress()->setIsDefaultBilling('1');
                $customer_obj->getDefaultShippingAddress()->save();
                $default_shipping_address = $get_customer_address[$defaultShippingId];
            } else {
                $default_shipping_address = $default_billing_address;
            }
            //create header json

            $json_array = [];
            $json_array['customerName'] = $this->makeShiptoName($default_billing_address);
            $json_array['sourceCustomerID'] = $customer_id;
            $json_array['email'] = $get_customer_detail['email'];
            $json_array['contact_Firstname'] = $get_customer_detail['firstname'];
            $json_array['contact_Lastname'] = $get_customer_detail['lastname'];
            $json_array['homePhone'] = $default_billing_address['telephone'];
            $json_array['workPhone'] = '';
            $json_array['mobilePhone'] = $default_billing_address['telephone'];
            $json_array['telephone'] = $default_billing_address['telephone'];
            $json_array['customerTypeCode'] = 'Sandpiper Web Customer';

            //billing address

            $country_name = $this->getCountryname($default_billing_address['country_id']);
            $countryclass_by_mapping = $this->getCountryMappingForInterprise($country_name);
            $country_name_mapped = $countryclass_by_mapping['interprise_country'];
            $classcode_mapped = $countryclass_by_mapping['interprise_customer_class'];
            $shiptoclasscode_mapped = $countryclass_by_mapping['interprise_shipto_class'];
            if ($isVatExempt) {
                $classcode_mapped = 'CCLS-000484';
            }
            $json_array['billingAddress'] = [
                'address' => str_replace("\n", ",\r\n", $default_billing_address['street']),
                'city' => $default_billing_address['city'],
                'county' => $default_billing_address['region'],
                'postalCode' => strtoupper($default_billing_address['postcode']),
                'country' => $country_name_mapped,
                'classcode' => $classcode_mapped,
            ];

            //shipping address

            $country_name = $this->getCountryname($default_shipping_address['country_id']);
            $countryclass_by_mapping = $this->getCountryMappingForInterprise($country_name);
            $country_name_mapped = $countryclass_by_mapping['interprise_country'];
            $classcode_mapped = $countryclass_by_mapping['interprise_customer_class'];
            $shiptoclasscode_mapped = $countryclass_by_mapping['interprise_shipto_class'];
            if ($isVatExempt) {
                $shiptoclasscode_mapped = 'SHPCLS-000484';
            }
            $json_array['shippingAddress'] = [
                'address' => str_replace("\n", ",\r\n", $default_shipping_address['street']),
                'city' => $default_shipping_address['city'],
                'county' => $default_shipping_address['region'],
                'postalCode' => strtoupper($default_shipping_address['postcode']),
                'country' => $country_name_mapped,
                'classcode' => $shiptoclasscode_mapped,
            ];
            if ($isVatExempt) {
                if (isset($get_customer_detail['vatexemptexpirydate_c'])) {
                    $vatExpiryDate = $get_customer_detail['vatexemptexpirydate_c'];
                } else {
                    $currentDate = $this->getCurrentTime();
                    $vatExpiryDate = date('Y-m-d H:i:s', strtotime('+3 years', strtotime($currentDate)));
                }
                $customFields[] = ["field" => "VATExempt_C", "value" => true];
                $customFields[] = ["field" => "VATExemptExpiryDate_C", "value" => $vatExpiryDate];
                $json_array['customFields'] = $customFields;
            }
            echo $json = json_encode(
                $json_array,
                JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE
            );

            $result_post = $this->postCurlData('Customer', $json);
            $decoded = json_decode($result_post['result'], true);

            //echo '<br/>result_post';
            //print_r($result_post);
            //die;
            if (is_array($decoded) && array_key_exists('errors', $decoded)) {
                $update_data['Status'] = 'fail';
                $update_data['Request'] = $json;
                $update_data['Remarks'] = $result_post['result'];
                $update_data['ActivityTime'] = $this->getCurrentTime();

                return $update_data;
            } else {
                $result = $result_post['result'];

                $customer_id = $this->getStringBetween($result, "CustomerCode ", " has been created.");
                if ($customer_id != '') {
                    $customer_data_inteprise = $this->getCurlData('customer?customercode=' . $customer_id);
                    $data_interprise = $customer_data_inteprise['results']['data']['attributes'];
                    $interprise_ship_to_code = $data_interprise['defaultShipToCode'];
                    $interprise_contact_code = $data_interprise['defaultContact'];
                    try {
                        $custom = $this->customerFactory->create();
                        $custom = $custom->setWebsiteId(1);
                        $custom = $custom->load($magento_customer_id);
                        $customerData = $custom->getDataModel();
                        $customerData->setCustomAttribute('interprise_customer_code', $customer_id);
                        if ($isVatExempt) {
                            $customerData->setCustomAttribute('vat_exempt_shipto_code', $interprise_ship_to_code);
                        } else {
                            $customerData->setCustomAttribute('with_vat_shipto_code', $interprise_ship_to_code);
                        }
                        $custom->updateData($customerData);
                        $custom->save();
                        ///$this->updateShiptoAddressAttribute($default_array['default_shipping_id'],$interprise_ship_to_code);
                        //$default_shipping_address_obj->setShipToCode($interprise_ship_to_code);
                        //$default_shipping_address_obj->save();
                    } catch (Exception $ex) {
                        $update_data['Status'] = 'Fail';
                        $update_data['Request'] = $json;
                        $update_data['Remarks'] = $ex->getMessage();
                        $update_data['ActivityTime'] = $this->getCurrentTime();

                        return $update_data;
                    } catch (\Magento\Framework\Validator\Exception $ex1) {
                        $update_data['Status'] = 'Fail';
                        $update_data['Request'] = $json;
                        $update_data['ActivityTime'] = $this->getCurrentTime();
                        $update_data['Remarks'] = $ex1->getMessage() . " in function " . __METHOD__;

                        return $update_data;
                    }
                } else {
                    $update_data['Status'] = 'Fail';
                    $update_data['Request'] = $json;
                    $update_data['ActivityTime'] = $this->getCurrentTime();
                    $update_data['Remarks'] = 'Posted in Interprise but customer code not found from API';

                    return $update_data;
                }
            }
            $update_data['entity_id'] = $customer_id;
            $update_data['Request'] = $json;
            $update_data['Status'] = 'Success';
            $update_data['ActivityTime'] = $this->getCurrentTime();
            $update_data['Remarks'] = $result;

            // } else {
            //     $update_data['Status'] = 'fail';
            //     $update_data['Request']=$json;
            //     $update_data['ActivityTime'] = $this->getCurrentTime();
            //     $update_data['Remarks'] = 'No Address Found';

            // }
        }

        return $update_data;
    }

    public function getCustomerAddressFromStore($customer_obj)
    {
        $array = [];
        foreach ($customer_obj->getAddresses() as $address) {
            $address_arr = $address->toArray();
            $array[$address_arr['entity_id']] = [
                'entity_id' => $address_arr['entity_id'],
                'parent_id' => $address_arr['parent_id'],
                'is_active' => $address_arr['is_active'],
                'city' => $address_arr['city'],
                'company' => $address_arr['company'],
                'country_id' => $address_arr['country_id'],
                'firstname' => $address_arr['firstname'],
                'lastname' => $address_arr['lastname'],
                'middlename' => $address_arr['middlename'],
                'postcode' => strtoupper($address_arr['postcode']),
                'prefix' => $address_arr['prefix'],
                'region' => $address_arr['region'],
                'region_id' => $address_arr['region_id'],
                'street' => $address_arr['street'],
                'telephone' => $address_arr['telephone'],
                'increment_id' => $address_arr['increment_id'],
            ];
        }

        return $array;
    }

    public function makeShiptoName($array = [])
    {
        $shipToname = '';
        if (isset($array['company']) && $array['company'] != '') {
            $shipToname .= trim($array['company']) . ' FAO: ';
        }
        if (isset($array['firstname']) && $array['firstname'] != '') {
            $shipToname .= trim($array['firstname']);
        }
        if (isset($array['lastname']) && $array['lastname'] != '') {
            if ($array['lastname'] != '-') {
                $shipToname .= '  ' . trim($array['lastname']);
            }
        }

        return $shipToname;
    }

    public function getCountryname($countryCode)
    {
        $country = $this->_countryFactory->create()->loadByCode($countryCode);

        return $country->getName();
    }

    public function editShippingAddress($address_object, $_customer_object, $default_address_array)
    {
        $email1 = $_customer_object->getEmail();
        if ($_customer_object->getData('interprise_customer_code')) {
            /*             * ************************ start update contact of customer *************************** */
            $data['customerCode'] = $_customer_object->getData('interprise_customer_code');
            $array_make = [
                'firstname' => $address_object['firstname'],
                'lastname' => $address_object['lastname'],
                'company' => $address_object['company'],
            ];
            $shipToName = $this->makeShiptoName($array_make);
            $contury_name = $this->getCountryname($address_object['country_id']);
            $countryclass_by_mapping = $this->getCountryMappingForInterprise($contury_name);
            $country_name_mapped = $countryclass_by_mapping['interprise_country'];
            $classcode_mapped = $countryclass_by_mapping['interprise_customer_class'];
            $shiptoclasscode_mapped = $countryclass_by_mapping['interprise_shipto_class'];

            $data['shippingAddress'] = [
                'shipToCode' => $address_object['interprise_shiptocode'],
                'shipToName' => $shipToName,
                'address' => str_replace("\n", "\r\n", $address_object['street']),
                'city' => $address_object['city'],
                'county' => $address_object['region'],
                'postalCode' => strtoupper($address_object['postcode']),
                'telephone' => $address_object['telephone'],
                'email' => '',
                //'fax' => $address_object->getData('fax'),
                'country' => $country_name_mapped,
                'classcode' => $shiptoclasscode_mapped,
            ];
            $result = $this->putCurlData(
                'Customer',
                json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE)
            );
            if ($result['api_error']) {
                $status['Status'] = true;
                $status['error'] = '';
                $status['entity_id'] = json_encode($data);

                return $status;
            } else {
                $status['Status'] = false;
                $status['error'] = 'In method ' . __METHOD__ . ' ' . json_encode($result_array['errors']);
                $status['entity_id'] = json_encode($data);

                return $status;
            }
        }
    }

    public function addNewShippingAddress($address_object, $_customer_object, $default_array)
    {
        $data['customerCode'] = $_customer_object->getData('interprise_customer_code');
        //$data['customerName'] = $this->getCustomerName($_customer_object->getData('interprise_customer_id'));
        if ($address_object['entity_id'] != $default_array['default_shipping_id']) {
            $data['defaultShipTo'] = false;
        } else {
            $data['defaultShipTo'] = true;
        }
        $array_make = [
            'firstname' => $address_object['firstname'],
            'lastname' => $address_object['lastname'],
            'company' => $address_object['company'],
        ];
        $shipToName = $this->makeShiptoName($array_make);

        $contury_name = $this->getCountryname($address_object['country_id']);
        $countryclass_by_mapping = $this->getCountryMappingForInterprise($contury_name);
        $country_name_mapped = $countryclass_by_mapping['interprise_country'];
        $classcode_mapped = $countryclass_by_mapping['interprise_customer_class'];
        $shiptoclasscode_mapped = $countryclass_by_mapping['interprise_shipto_class'];
        $data['newShipToDetails'] = [
            'shipToName' => $shipToName,
            'address' => str_replace("\n", "\r\n", $address_object['street']),
            'city' => $address_object['city'],
            'county' => $address_object['region'],
            'postalCode' => strtoupper($address_object['postcode']),
            'telephone' => $address_object['telephone'],
            //'fax' => $address_object->getData('fax'),
            'country' => $country_name_mapped,
            'classcode' => $shiptoclasscode_mapped,
            'email' => '',
            //'email'=> $address_object->getData('is_email')
        ];
        $result = $this->putCurlData('Customer/AddShipTo', json_encode($data));
        if ($result['api_error']) {
            $result_array = json_decode($result['result'], true);
            if (is_array($result_array) && array_key_exists('errors', $result_array)) {
                $status['Status'] = false;
                $status['error'] = $result_array['errors'];
                $status['entity_id'] = json_encode($data);

                return $status;
            } else {
                try {
                    $fullstring = $result['result'];
                    $shiptocode_generated = $this->getStringBetween($fullstring, "New ShipTo ", " has been added in");
                    $address = $this->_addressFactory->create();
                    $address->load($address_object['entity_id']);
                    $address->setInterpriseShiptocode($shiptocode_generated);
                    //$address = $this->addressRepository->getById($address_object['entity_id']);
                    //$address->setInterpriseShiptoCode($shiptocode_generated);
                    //$this->addressRepository->save($address);
                    $address->save();
                } catch (Exception $e) {
                    $status['Status'] = false;
                    $status['error'] = $e->getMessage();
                    $status['entity_id'] = '';

                    return $status;
                }

                $status['Status'] = true;
                $status['error'] = '';
                $status['entity_id'] = json_encode($data);

                return $status;
            }
        } else {
            $status['Status'] = false;
            $status['error'] = 'In method ' . __METHOD__ . ': Error in pushing Interprise API';
            $status['entity_id'] = '';

            return $status;
        }
    }
}
