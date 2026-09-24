<?php

namespace Interprise\Logger\Helper;

use \Interprise\Logger\Helper\Data;
use Magento\Framework\App\Helper\Context;

//use Magento\Setup\Exception;
use Magento\Framework\Validator\Exception;

class Customers extends Data
{

    /**
     * @return void
     */
    const XML_PATH_TRANSACTION_INVALID_CUSTOMER_EMAILID = 'setup/email_settings/invalid_emaiaddress';

    public $datahelper;

    public $storeManager;

    public $customerFactory;

    public $customer;

    public $connection;

    public $resource;

    public $defshippingadd;

    protected $group;

    protected $_region;

    protected $_countryFactory;

    protected $_custaddressFactory;

    protected $_activityScFactory;

    protected $reattemptorder;

    protected $registry;

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
        \Magento\Customer\Model\Group $group,
        \Interprise\Logger\Model\ResourceModel\Countryclassmapping\CollectionFactory $countrymap,
        \Magento\Directory\Model\Region $region,
        \Interprise\Logger\Model\CronActivityScheduleFactory $activityScheduleFactory,
        \Interprise\Logger\Helper\OrderReattempt $_orderReattempt,
        \Magento\Customer\Model\ResourceModel\Address\CollectionFactory $custaddress,
        \Magento\Framework\Registry $registry
    ) {
        $this->storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->customer = $customer;
        $this->group = $group;
        $this->_region = $region;
        $this->_countryFactory = $countrymap;
        $this->_custaddressFactory = $custaddress;
        $this->_activityScFactory = $activityScheduleFactory;
        $this->defshippingadd = '';
        $this->reattemptorder = $_orderReattempt;
        $this->registry = $registry;
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

    public function customerSingle($datas)
    {
        echo '<br/>' . $dataId = $datas['DataId'];
        try {
            $api_responsc = $this->getCurlData('customer?customercode=' . $dataId);
            $update_data['ActivityTime'] = $this->getCurrentTime();
            $update_data['Request'] = $api_responsc['request'];

            if (isset($api_responsc['results'])) {
                if (isset($api_responsc['results']['data'])) {
                    $update_data['Response'] = json_encode($api_responsc['results']['data']);
                } else {
                    $update_data['Response'] = "Entity not found";
                }
            } else {
                $update_data['Response'] = "Entity not found";
            }

            if ($api_responsc['api_error']) {
                $data = $api_responsc['results']['data']['attributes'];

                $allowedCustomerTypes = ['sandpiper mo customer', 'sandpiper web customer', 'sandpiper wh customer'];
                $customerTypeCode = strtolower(trim($data['customerTypeCode']));

                if (!in_array($customerTypeCode, $allowedCustomerTypes)) {
                    $update_data['Status'] = 'CustomerType not allowed';
                    $update_data['Response'] = "'" . $data['customerTypeCode'] . "' not allowed.";
                    $update_data['Remarks'] = "'" . $data['customerTypeCode'] . "' not allowed.";
                } else {
                    $emailok = false;

                    if (isset($data['email']) && $data['email'] != '') {
                        $emailok = $this->validEmail(trim($data['email']));
                    }

                    if ($emailok) {
                        $email_id = trim($data['email']);
                    } else {
                        //$email_id = $dataId."-info@interprise.co.uk";
                        $content = [];
                        $eml = isset($data['email']) ? trim($data['email']) : 'Blank email';
                        $content['EmailID'] = $eml;
                        $content['CustomerCode'] = $dataId;
                        $content['Msg'] = "Unable to sync customer because '" . $eml . "' is not a valid emailaddress for " . $dataId . " on Interprise. Add a valid, unique email address in interprise.";
                        $this->reattemptorder->sendmessage(self::XML_PATH_TRANSACTION_INVALID_CUSTOMER_EMAILID, $content);
                        $update_data['Status'] = 'fail';
                        $update_data['Remarks'] = $eml . " is not a valid customer emailaddress. Add a valid, unique email address in interprise.";

                        return $update_data;
                    }

                    // If email Id has spaces then update valid emailaddress on Interprise ///////////////
                    if (strpos($data['email'], " ") !== false) {
                        $updateresult = $this->updateCustomerEmail(trim($email_id), $dataId);
                        if (strtolower($updateresult['Status']) != "success") {
                            $update_data['Status'] = 'fail';
                            $update_data['Remarks'] = $updateresult['Remarks'];

                            return $update_data;
                        }
                    }
                    $actions = $this->checkCustomerExistence($dataId, $email_id);
                    echo '<pre>';
                    print_r($actions);
                    echo '<br/>' . $customerCreatedBy = strtolower(trim($data['userCreated']));

                    if ($actions['action'] == 'INSERT') {
                        if ($customerCreatedBy != 'api') {
                            $email = $actions['email_id'];
                            if ($email == '') {
                                $content = [];
                                $content['EmailID'] = $email_id;
                                $content['CustomerCode'] = $dataId;
                                $content['Msg'] = "Unable to sync customer because '" . $email_id . "' email address is already assigned to another customer. Add a valid, unique email address in interprise.";
                                try {
                                    $this->reattemptorder->sendmessage(self::XML_PATH_TRANSACTION_INVALID_CUSTOMER_EMAILID, $content);
                                } catch (Magento\Setup\Exception $e) {
                                }

                                //$update_data['Status'] = 'fail';
                                //$update_data['Remarks'] = $data['email']." is not a valid customer emailaddress is not valid. ";

                                $err_msg = "Email Address Already Exist " . $email_id . " Use another email id for this customer. ";
                                $update_data['Status'] = false;
                                $update_data['Remarks'] = $err_msg;
                                $update_data['entity_id'] = '';

                                return $update_data;
                            }
                            if (isset($data['customerTypeCode'])) {
                                if (strtolower($data['customerTypeCode']) != 'sandpiper wh customer') {
                                    $customer_id['Status'] = false;
                                    $customer_id['error'] = "Not a wholesale customer";
                                    $update_data['Status'] = 'fail';
                                    $update_data['Remarks'] = "Not a wholesale customer";
                                    $update_data['entity_id'] = '';

                                    return $update_data;
                                }
                            }

                            $customer_id = $this->createCustomer($data, $email);
                        } else {
                            ///////// Update customer code in Magento using email address /////////////

                            $magento_customer_id = $this->getCustomerIdByEmail($email_id);
                            if ($magento_customer_id) {
                                try {
                                    $update_customer_factory = $this->customerFactory->create();
                                    $update_customer = $update_customer_factory->load($magento_customer_id);
                                    $customerData = $update_customer->getDataModel();
                                    $customerData->setCustomAttribute('interprise_customer_code', $dataId);
                                    $update_customer->updateData($customerData);
                                    $update_customer->save();
                                    $customer_id['Status'] = true;
                                } catch (Exception $ex) {
                                    $customer_id['Status'] = false;
                                    $customer_id['error'] = $ex->getMessage();
                                    $update_data['Status'] = 'fail';
                                    $update_data['Remarks'] = $ex->getMessage();
                                }
                            } else {
                                $customer_id['Status'] = false;
                                $customer_id['error'] = "Customer not found on Magento with email address " . $email_id;
                                $update_data['Status'] = 'fail';
                                $update_data['Remarks'] = "Customer not found on Magento with email address " . $email_id;
                            }
                            ///////////////////// End update customer code ////////////////////////////
                        }
                    } else if ($actions['action'] == 'UPDATE') {
                        $customerIdCheck = $actions['customer_id'];
                        $customer_id = $this->updateCustomer($data, $customerIdCheck);
                    }
                    if ($customer_id['Status']) {
                        $update_data['Status'] = 'Success';
                        $update_data['Remarks'] = 'Success';
                        if (isset($customer_id['Remarks'])) {
                            $update_data['Remarks'] = $customer_id['Remarks'];
                        }
                    } else {
                        $update_data['Status'] = 'fail';
                        $update_data['Remarks'] = json_encode($customer_id['error']);
                    }

                }
            } else {
                $update_data['Status'] = 'fail';
                $update_data['Remarks'] = $api_responsc['results'];
            }
        } catch (Exception $ex) {
            $update_data['Status'] = 'fail';
            $update_data['Remarks'] = $ex->getMessage();
        }

        return $update_data;
    }

    public function validEmail($email)
    {
        $return = false;
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $return = true;
        }

        return $return;
    }

    public function updateCustomerEmail($email_id, $dataId)
    {
        if ($email_id != '' && $dataId != '') {
            $update_json_array = [
                'customerCode' => $dataId,
                'email' => $email_id,
            ];
            try {
                $result_update_customer = $this->putCurlData(
                    'customer',
                    json_encode(
                        $update_json_array,
                        JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE
                    )
                );
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
                    $update_data['Remarks'] = "Customer " . $dataId . " updated";
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
        }
    }

    public function checkCustomerExistence($customer_code, $email_id)
    {
        $customercollection = $this->customerFactory->create()->getCollection()
            ->addAttributeToSelect("*")
            ->addAttributeToFilter("interprise_customer_code", ["eq" => $customer_code])
            ->load();
        if ($customercollection->count() > 0) {
            $data = $customercollection->getFirstItem();

            return ['action' => 'UPDATE', 'customer_id' => $data['entity_id'], 'email_id' => $email_id];
        } else {
            $resultss = $this->getCustomerIdByEmail($email_id);
            if ($resultss) {
                //$new_email = $customer_code . "-" . $email_id;
                $new_email = '';
            } else {
                $new_email = $email_id;
            }

            return ['action' => 'INSERT', 'customer_id' => '', 'email_id' => $new_email];
        }
    }

    public function getCustomerIdByEmail($email, $websiteId = 1)
    {
        $customer = $this->customerFactory->create();
        $customer->setWebsiteId($websiteId);
        $customer->loadByEmail($email);// load customer by email address
        $customer_id = $customer->getEntityId();
        if ($customer_id) {
            return $customer_id;
        } else {
            return false;
        }
    }

    public function createCustomer($data, $email)
    {
        if (!$data['isActive']) {
            $status['Status'] = false;
            $status['error'] = "Inactive Customer on Interprise.";
            $status['entity_id'] = '';

            return $status;
        }
        $email = trim($email);
        $customer_name_arr = $this->makeCustomerNameWihCompany($data['customerName']);
        $first_name = $customer_name_arr['first_name'];
        $last_name = $customer_name_arr['last_name'];
        $company = $customer_name_arr['company'];
        $is_customer_code = $data['customerCode'];
        $customer_group_id = $this->getCustomerGroupIdByName($data['defaultPrice']);

        $customerTypeCode = strtolower(trim($data['customerTypeCode']));
        $websiteId = 1;
        if ($customerTypeCode == 'sandpiper wh customer') {
            $websiteId = 2;
        }
        $store = $this->storeManager->getStore($websiteId);

        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $this->customerFactory->create();

        $customer->setWebsiteId($websiteId)->setStore($store);

        $customer->setGroupId($customer_group_id);
        $customer->setEmail($email);
        $customer->setFirstname($first_name);

        if (trim($last_name) == '') {
            $last_name = '--';//to be deleted
        }
        $customer->setLastname($last_name);
        $customer->setPassword('123456');

        $customerData = $customer->getDataModel();
        $customerData->setCustomAttribute('interprise_customer_code', $is_customer_code);
        if (isset($data['shippingMethodGroup'])) {
            $customerData->setCustomAttribute('interprise_smethod_group', $data['shippingMethodGroup']);
        }
        if (isset($data['shippingMethod'])) {
            $customerData->setCustomAttribute('interprise_shippingmethod', $data['shippingMethod']);
        }
        if (isset($data['paymentTermGroup'])) {
            $customerData->setCustomAttribute('interprise_ptgroup', $data['paymentTermGroup']);
        }
        if (isset($data['paymentTermCode'])) {
            $customerData->setCustomAttribute('interprise_ptcode', $data['paymentTermCode']);
        }
        if (isset($data['taxCode'])) {
            $customerData->setCustomAttribute('interprise_taxcode', $data['taxCode']);
        }
        if (isset($data['pricingMethod'])) {
            $customerData->setCustomAttribute('interprise_pricingmethod', $data['pricingMethod']);
        }
        if (isset($data['pricingLevel'])) {
            $customerData->setCustomAttribute('interprise_pricinglevel', $data['pricingLevel']);
        }
        if (isset($data['defaultShipToCode'])) {
            $this->defshippingadd = $data['defaultShipToCode'];
            $customerData->setCustomAttribute('interprise_defaultshiptocode', $data['defaultShipToCode']);
        }
        if (isset($data['defaultPrice'])) {
            $customerData->setCustomAttribute('interprise_defaultprice', $data['defaultPrice']);
        }
        if (isset($data['customerTypeCode'])) {
            $customerData->setCustomAttribute('interprise_customertypecode', $data['customerTypeCode']);
        }
        if (isset($data['businessType'])) {
            $customerData->setCustomAttribute('interprise_businesstype', $data['businessType']);
        }
        if (isset($data['discount'])) {
            $customerData->setCustomAttribute('interprise_discount', $data['discount']);
        }
        if (isset($data['creditLimit'])) {
            $customerData->setCustomAttribute('interprise_creditlimit', $data['creditLimit']);
        }
        if (isset($data['credit'])) {
            $customerData->setCustomAttribute('interprise_credit', $data['credit']);
        }
        if (isset($data['currencyCode'])) {
            $customerData->setCustomAttribute('interprise_currencycode', $data['currencyCode']);
        }
        if (isset($data['pricingPercent'])) {
            $customerData->setCustomAttribute('interprise_pricingpercent', $data['pricingPercent']);
        }
        if (isset($data['isCreditHold'])) {
            $customerData->setCustomAttribute('interprise_iscredithold', $data['isCreditHold']);
        }
        if (isset($data['isAllowBackOrder'])) {
            $customerData->setCustomAttribute('interprise_isallowbackorder', $data['isAllowBackOrder']);
        }
        if (isset($data['isWebAccess'])) {
            $customerData->setCustomAttribute('interprise_iswebaccess', $data['isWebAccess']);
        }
        if (isset($data['classCode'])) {
            $customerData->setCustomAttribute('interprise_classcode', $data['classCode']);
        }

        $vatExempt = false;
        if (isset($data['customFields'])) {
            foreach ($data['customFields'] as $customData) {
                if (strtolower($customData['field']) == 'vatexempt_c' && strtolower($customData['value']) == true) {
                    $vatExempt = true;
                    break;
                }
            }

            if ($vatExempt) {
                foreach ($data['customFields'] as $customData) {
                    if (strtolower($customData['field']) == 'vatexemptexpirydate_c') {
                        if ($customData['value'] != '' && $customData['value'] != null) {
                            $customerData->setCustomAttribute('vatexemptexpirydate_c', trim($customData['value']));
                        } else {
                            $customerData->setCustomAttribute('vatexemptexpirydate_c', '2099-01-01 00:00:00');
                        }
                        break;
                    }
                }
            }
        }
        $customerData->setCustomAttribute('interprise_apicreated', 1);

        try {
            $customer->updateData($customerData);
            $exists = $this->customerDuplicates($email);
            if ($exists == "not found") {
                $customer->save();
                $magento_customer_id = $customer->getId();
                $billing_address_creation = $this->makeBillingAddress($data, $magento_customer_id);
                $shipping_address_creation = $this->makAddresses($is_customer_code, $magento_customer_id);
                $status['Status'] = true;
                $status['error'] = "";
                $status['entity_id'] = $magento_customer_id;

                return $status;
            } else {
                $status['Status'] = true;
                $status['error'] = "";
                $status['entity_id'] = $exists;

                return $status;
            }
        } catch (Exception $e) {
            $err_msg = $e->getMessage();
            $status['Status'] = false;
            $status['error'] = "Err msg - " . $err_msg . " in function " . __METHOD__;
            $status['entity_id'] = '';

            return $status;
        }
    }

    public function makeCustomerNameWihCompany($name)
    {
        $shiptoName = $name;
        $last_name = '';
        $company_name = '';
        if (strpos($shiptoName, "FAO:") !== false) {
            $explode_withcomp = explode("FAO:", $shiptoName);
            $company_name = $explode_withcomp[0];
            $name_merged = $explode_withcomp[1];
            $name_exploded = explode('  ', $name_merged);
            $last_name = '';
            $first_name = $name_exploded[0];
            if (isset($name_exploded[1]) && $name_exploded[1] != '') {
                $last_name = $name_exploded[1];
            }
        } else {
            $exploded = explode('  ', $shiptoName);
            $first_name = $exploded[0];
            array_shift($exploded);
            if (count($exploded) > 0) {
                $last_name = implode(' ', $exploded);
            }
        }
        if ($last_name == '') {
            $last_name = '-';
        }
        $array = [
            'first_name' => trim($first_name),
            'last_name' => trim($last_name),
            'company' => trim($company_name),
        ];

        return $array;
    }

    public function getCustomerGroupIdByName($customerGroupName)
    {
        $groupObj = $this->group;

        $existingGroup = $groupObj->load($customerGroupName, 'customer_group_code');
        if ($existingGroup->getId()) {
            return $existingGroup->getId();
        } else {
            return 1;
        }
    }

    public function customerDuplicates($email_id)
    {
        $customer_exist = $this->getCustomerIdByEmail($email_id);
        if ($customer_exist) {
            return $customer_exist;
        } else {
            return "not found";
        }
    }

    public function makeBillingAddress($data, $customer_id)
    {
        echo "<br/>Inside makeBillingAddress";
        $customer_obj = $this->customerFactory->create()->load($customer_id);
        echo '<pre>';
        print_r($customer_obj->getData());
        echo '</pre>';
        $customername = $data['customerName'];
        if (isset($data['address'])) {
            $addressdata = $data['address'];
        } else {
            $addressdata = "NOADDRESSFOUND";
        }
        if (isset($data['city'])) {
            $city = $data['city'];
        } else {
            $city = "NA";
        }
        if (isset($data['postalCode'])) {
            $postalCode = $data['postalCode'];
        } else {
            $postalCode = "NA";
        }
        $country = $data['country'];
        if (isset($data['telephone'])) {
            $telephone = $data['telephone'];
        } else {
            $telephone = '999999999';
        }

        $telephone = preg_replace('/\s+/', '', $telephone);
        if (isset($data['email'])) {
            $email = $data['email'];
        } else {
            $email = '';
        }

        if (isset($data['county'])) {
            $region = $data['county'];
        }
        if (isset($data['postalCode'])) {
            $postalcode = $data['postalCode'];
        } else {
            $postalcode = "NA";
        }

        $name_shipto_arr = $this->makeCustomerNameWihCompany($customername);
        $defaultBillingId = '';
        $defaultShippingId = '';

        $customerData = $customer_obj->getData();
        if (isset($customerData['default_billing']) && $customerData['default_billing'] != '') {
            $defaultBillingId = $customerData['default_billing'];
        }
        // if ($customer_obj->getDefaultBillingAddress()) {
        //     echo '<br/>Inside getDefaultBillingAddress condition';
        //     echo '<pre>';
        //     print_r($customer_obj->getDefaultBillingAddress()->getData());
        //     echo '</pre>';
        //     $defaultBillingId = $customer_obj->getDefaultBillingAddress()->getId();
        // }
        $country_name = $country;
        $country_code = $this->getISOCodeCountryByName($country_name);

        $address = $this->_addressFactory->create();
        $address = $address->load($defaultBillingId);
        echo '<br/>defaultBillingId' . $defaultBillingId;

        try {
            $my_string = preg_replace(['/\r\n/'], '#PH', $addressdata);
            $exploded = explode('#PH', $my_string);
            $second_line = '';
            $first_line = trim($exploded[0]);
            if (isset($exploded[1]) && $exploded[1] != '') {
                $second_line = "\r\n" . trim($exploded[1]);
            }
            if (isset($exploded[2]) && $exploded[2] != '') {
                $second_line .= "," . trim($exploded[2]);
            }
            $addressdata = $first_line . $second_line;
            $last_name = $name_shipto_arr['last_name'];
            if (trim($last_name) == '') {
                $last_name = '--';//to be deleted
            }
            $address->setCustomerId($customer_id)
                ->setFirstname($name_shipto_arr['first_name'])
                ->setLastname($last_name)
                ->setCountryId($country_code)
                ->setPostcode($postalcode)
                ->setCity($city)
                ->setTelephone($telephone)
                ->setCompany($name_shipto_arr['company'])
                ->setStreet(trim($addressdata));
            if (isset($data['county'])) {
                $regionObj = $this->_region;
                $regionId = $regionObj->loadByCode(ucfirst($data['county']), $country_code)->getId();
                $address->setRegionId($regionId);
            }
            if (isset($data['state'])) {
                $regionObj = $this->_region;
                $regionId = $regionObj->loadByCode($data['state'], $country_code)->getId();
                $address->setRegionId($regionId);
            }
            $address->setIsDefaultBilling('1')
                ->setInterpriseShiptocode('billing')
                ->setInterpriseShippingmethodgroup($data['shippingMethodGroup'])
                ->setInterpriseShippingmethod($data['shippingMethod'])
                ->setInterpriseFreighttax($data['taxCode'])
                ->setInterpriseApicreatedaddress('1')
                ->setSaveInAddressBook('1')
                ->save();
            echo '<br/>' . $address->getId();
        } catch (Exception $e) {
            $var = "new";
        }
    }

    public function getISOCodeCountryByName($countryname)
    {
        $countcol = $this->_countryFactory->create();
        $countcol->addFieldToFilter('interprise_country', ['eq' => $countryname]);
        if ($countcol->count() > 0) {
            foreach ($countcol as $cvn) {
                $data = $countcol->getFirstItem();

                return $data['iso_code'];
            }
        } else {
            return 'GB';
        }
    }

    public function makAddresses($customer_code, $magento_customer_id)
    {
        $api_responsc = $this->getCurlData('customer/shipto?customercode=' . $customer_code);
        $message_result = '';
        $delete_shipto_address_array = [];//that shiptos which are not more active in Interprise
        if ($api_responsc['api_error']) {
            $data_shipto = $api_responsc['results']['data'];
            if (count($data_shipto) > 0) {
                foreach ($data_shipto as $key_shipto => $value_shipto) {
                    $attributes_shipto = $value_shipto['attributes'];

                    if (!$attributes_shipto['isActive']) {/*if disabled in Interprise will delete this address in
 Magento also*/
                        $address_id = '';

                        $delete_shipto_address = $attributes_shipto['shipToCode'];
                        $address_id = $this->checkShippingAddressExist($delete_shipto_address);
                        if ($address_id == '' || !isset($address_id)) {
                            $a = '1';
                        } else {
                            $address = $this->_addressFactory->create();
                            $address->load($address_id);
                            $address->delete();
                        }
                    } else {
                        $shiptoname = $attributes_shipto['shipToName'];
                        $name_shipto_arr = $this->makeCustomerNameWihCompany($shiptoname);
                        $first_name = $name_shipto_arr['first_name'];
                        $last_name = $name_shipto_arr['last_name'];
                        if ($last_name == '') {
                            $last_name = $first_name;
                        }
                        $company = $name_shipto_arr['company'];
                        if (isset($attributes_shipto['postalCode'])) {
                            $postal_code = $attributes_shipto['postalCode'];
                        } else {
                            $postal_code = 'NA';
                        }
                        $city = '';
                        if (isset($attributes_shipto['city'])) {
                            $city = $attributes_shipto['city'];
                        } else {
                            $city = "NA";
                        }
                        $region = '';
                        if (isset($attributes_shipto['county'])) {
                            $region = $attributes_shipto['county'];
                        }

                        $countrystate = '';
                        if (isset($attributes_shipto['state'])) {
                            $countrystate = $attributes_shipto['state'];
                        }

                        if (isset($attributes_shipto['telephone']) && $attributes_shipto['telephone'] != '') {
                            $telephone = $attributes_shipto['telephone'];
                        } else {
                            $telephone = 9999999999;
                        }
                        if (isset($attributes_shipto['address'])) {
                            $address = $attributes_shipto['address'];
                        } else {
                            $address = 'NA';
                        }

                        $my_string = preg_replace(['/\r\n/'], '#PH', $address);
                        $exploded = explode('#PH', $my_string);
                        $full_address = '';
                        $first_line = '';
                        $second_line = '';
                        $first_line = trim($exploded[0]);
                        if (isset($exploded[1]) && $exploded[1] != '') {
                            $second_line = "\r\n" . trim($exploded[1]);
                        }
                        if (isset($exploded[2]) && $exploded[2] != '') {
                            $second_line .= "," . trim($exploded[2]);
                        }
                        $address_for_magento = $first_line . $second_line;
                        $country_name = $attributes_shipto['country'];
                        $country_code = $this->getISOCodeCountryByName($country_name);
                        $shipto_exist = false;
                        $shipto_data = [];
                        $shipto_data['shiptoCode'] = $attributes_shipto['shipToCode'];
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
                        $shipto_data['interprise_shippingmethodgroup'] = $attributes_shipto['shippingMethodGroup'];
                        $shipto_data['interprise_shippingmethod'] = $attributes_shipto['shippingMethod'];
                        $shipto_data['interprise_freighttax'] = $attributes_shipto['taxCode'];
                        $shipto_process = $this->processShipto($shipto_data, $magento_customer_id);
                        if ($shipto_process['Status']) {
                            $message_result = '';
                        } else {
                            $message_result = $attributes_shipto['shipToCode'] . ' creation failed';
                        }
                    }
                }
            }
        }
    }

    public function checkShippingAddressExist($shipToCode)
    {
        $collections = $this->_custaddressFactory->create();
        $collections->addFieldToFilter('interprise_shiptocode', ['eq' => $shipToCode]);
        if ($collections->count() > 0) {
            $data = $collections->getFirstItem();

            return $data['entity_id'];
        } else {
            return false;
        }
    }

    public function processShipto($shipto_data, $magento_customer_id)
    {
        $address = $this->_addressFactory->create();
        $shipToCode = $shipto_data['shiptoCode'];
        echo '<br/>customer_address_id' . $customer_address_id = $this->checkShippingAddressExist($shipToCode);
        if ($customer_address_id) {
            $address->load($customer_address_id);
        }
        $last_name = $shipto_data['last_name'];
        if (trim($last_name) == '') {
            $last_name = '--';//to be deleted
        }
        $address->setCustomerId($magento_customer_id)
            ->setFirstname($shipto_data['first_name'])
            ->setLastname($last_name)
            ->setCountryId($shipto_data['country'])
            ->setPostcode($shipto_data['postalCode'])
            ->setCity($shipto_data['city'])
            ->setTelephone($shipto_data['telephone'])
            ->setCompany($shipto_data['company'])
            ->setStreet(trim($shipto_data['address']))
            ->setRegion($shipto_data['region']);
        if (isset($shipto_data['state']) || isset($shipto_data['region'])) {
            $regionObj = $this->_region;
            if (isset($shipto_data['state']) && $shipto_data['state'] != '') {
                $regionId = $regionObj->loadByCode(ucfirst($shipto_data['state']), $shipto_data['country'])->getId();
            } else {
                $regionId = $regionObj->loadByCode(ucfirst($shipto_data['region']), $shipto_data['country'])->getId();
            }
            $address->setRegionId($regionId);
        }
        if (isset($this->defshippingadd) && $this->defshippingadd != '' && $this->defshippingadd == $shipToCode) {
            $address->setIsDefaultShipping('1');
        }
        $address->setInterpriseShiptocode($shipto_data['shiptoCode']);
        $address->setInterpriseShippingmethodgroup($shipto_data['interprise_shippingmethodgroup']);
        $address->setInterpriseShippingmethod($shipto_data['interprise_shippingmethod']);
        $address->setInterpriseFreighttax($shipto_data['interprise_freighttax']);
        $address->setInterpriseApicreatedaddress('1');
        $address->setSaveInAddressBook('1');

        try {
            $address->save();
            $address_id = $address->getId();
            $arr = [];
            $arr['Status'] = true;
            $arr['entity_id'] = $address_id;

            return $arr;
        } catch (Exception $exception) {
            $arr = [];
            $arr['Status'] = false;
            $arr['entity_id'] = '';
            $arr['error'] = $exception->getMessage() . "in function " . __METHOD__;

            return $arr;
        }
    }

    public function updateCustomer($data, $customer_id)
    {
        try {
            $customerTypeCode = strtolower(trim($data['customerTypeCode']));
            $websiteId = 1;
            if ($customerTypeCode == 'sandpiper wh customer') {
                $websiteId = 2;
            }

            $update_customer_factory = $this->customerFactory->create();
            /** @var \Magento\Customer\Model\Customer $update_customer */
            $update_customer = $update_customer_factory->load($customer_id);
            if (!$data['isActive']) {
                try {
                    //echo '<br/>'.$this->state->getAreaCode();

                    //$this->registry->register('isSecureArea', true);

//                    $update_customer->delete();
                    $status['Status'] = true;
                    $status['error'] = "Inactive Customer on Interprise.";
                    $status['entity_id'] = $customer_id;

                    //$this->registry->unregister('isSecureArea');
                    return $status;
                } catch (Exception $e) {
                    $err_msg = $e->getMessage();
                    $status['Status'] = false;
                    $status['error'] = $err_msg . " in function " . __METHOD__;
                    $status['entity_id'] = $customer_id;

                    return $status;
                }
            }
            $customer_name = $this->makeCustomerNameWihCompany($data['customerName']);
            $customer_group_id = $this->getCustomerGroupIdByName($data['defaultPrice']);

            $store = $this->storeManager->getStore($websiteId);
            $update_customer->setWebsiteId($websiteId)->setStore($store);

            $update_customer->setGroupId($customer_group_id);
            $update_customer->setFirstname($customer_name['first_name']);
            $last_name = $customer_name['last_name'];
            if (trim($last_name) == '') {
                $last_name = '--';//to be deleted
            }
            $update_customer->setLastname($last_name);

            //////////////////////////////// Code to update emailID ////////////////////////////

            // $updateEmailID = $this->checkEmailIDExistance($data['customerCode'], $email_id);
            $emailok = false;
            //echo '<br/>'.$data['email'];

            if (isset($data['email']) && $data['email'] != '') {
                $emailok = $this->validEmail($data['email']);
            }

            if ($emailok) {
                $email_id = trim($data['email']);
            } else {
                //$email_id = $data['customerCode']."-info@interprise.co.uk";
                $content = [];
                $content['EmailID'] = $data['email'];
                $content['CustomerCode'] = $data['customerCode'];

                $content['Msg'] = "Unable to sync customer because " . $eml . " is not a valid emailaddress for " . $data['customerCode'] . " on Interprise. Add a valid, unique email address in interprise.";
                $this->reattemptorder->sendmessage(self::XML_PATH_TRANSACTION_INVALID_CUSTOMER_EMAILID, $content);

                $status['Status'] = false;
                $status['Remarks'] = $data['email'] . " is not a valid customer emailaddress. Add a valid, unique email address in interprise. ";
                $status['error'] = "Err msg - " . $data['email'] . " is not a valid emailaddress in function " . __METHOD__;
                $status['entity_id'] = '';

                return $status;
            }
            echo '<br/>' . $email_id;
            echo '<br/>' . $customerEmail = $update_customer->getEmail();
            if ($customerEmail != $email_id) {
                $updateEmailID = $this->checkEmailIDExistance($data['customerCode'], $email_id);
                if ($updateEmailID) {
                    //echo "Inside if";
                    $update_customer->setEmail($email_id);
                    //$update_customer->setPassword('123456');
                } else {
                    //echo "<br/>Inside Else";
                    $content = [];
                    $content['EmailID'] = $data['email'];
                    $content['CustomerCode'] = $data['customerCode'];
                    $content['Msg'] = "Unable to sync customer because '" . $data['email'] . "' email address is already assigned to another customer. Add a valid, unique email address in interprise.";
                    $this->reattemptorder->sendmessage(self::XML_PATH_TRANSACTION_INVALID_CUSTOMER_EMAILID, $content);
                    //$update_data['Status'] = 'fail';
                    //$update_data['Remarks'] = $data['email']." is not a valid customer emailaddress is not valid. ";

                    $err_msg = "Email Address Already Exist " . $data['email'] . " Use another email id for this customer. ";
                    $status['Status'] = false;
                    $status['error'] = $err_msg;
                    $status['entity_id'] = '';

                    return $status;
                }
            }

            //die;

            ///////////////////////////// End Code to update emailID ////////////////////////////

            $is_customer_code = $data['customerCode'];
            $update_customer->setUpdatedAt(date('Y-m-d h:i:s', strtotime($data['dateModified'])));
            $update_customer->setDisableAutoGroupChange(1);
            $update_customer->setConfirmation(null);
            $customerData = $update_customer->getDataModel();
            $customerData->setCustomAttribute('interprise_customer_code', $is_customer_code);
            if (isset($data['shippingMethodGroup'])) {
                $customerData->setCustomAttribute('interprise_smethod_group', $data['shippingMethodGroup']);
            }
            if (isset($data['shippingMethod'])) {
                $customerData->setCustomAttribute('interprise_shippingmethod', $data['shippingMethod']);
            }
            if (isset($data['paymentTermGroup'])) {
                $customerData->setCustomAttribute('interprise_ptgroup', $data['paymentTermGroup']);
            }
            if (isset($data['paymentTermCode'])) {
                $customerData->setCustomAttribute('interprise_ptcode', $data['paymentTermCode']);
            }
            if (isset($data['taxCode'])) {
                $customerData->setCustomAttribute('interprise_taxcode', $data['taxCode']);
            }
            if (isset($data['pricingMethod'])) {
                $customerData->setCustomAttribute('interprise_pricingmethod', $data['pricingMethod']);
            }
            if (isset($data['pricingLevel'])) {
                $customerData->setCustomAttribute('interprise_pricinglevel', $data['pricingLevel']);
            }
            if (isset($data['defaultShipToCode'])) {
                $this->defshippingadd = $data['defaultShipToCode'];
                $customerData->setCustomAttribute('interprise_defaultshiptocode', $data['defaultShipToCode']);
            }
            if (isset($data['defaultPrice'])) {
                $customerData->setCustomAttribute('interprise_defaultprice', $data['defaultPrice']);
            }
            if (isset($data['customerTypeCode'])) {
                $customerData->setCustomAttribute('interprise_customertypecode', $data['customerTypeCode']);
            }
            if (isset($data['businessType'])) {
                $customerData->setCustomAttribute('interprise_businesstype', $data['businessType']);
            }
            if (isset($data['discount'])) {
                $customerData->setCustomAttribute('interprise_discount', $data['discount']);
            }
            if (isset($data['creditLimit'])) {
                $customerData->setCustomAttribute('interprise_creditlimit', $data['creditLimit']);
            }
            if (isset($data['credit'])) {
                $customerData->setCustomAttribute('interprise_credit', $data['credit']);
            }
            if (isset($data['currencyCode'])) {
                $customerData->setCustomAttribute('interprise_currencycode', $data['currencyCode']);
            }
            if (isset($data['pricingPercent'])) {
                $customerData->setCustomAttribute('interprise_pricingpercent', $data['pricingPercent']);
            }
            if (isset($data['isCreditHold'])) {
                $customerData->setCustomAttribute('interprise_iscredithold', $data['isCreditHold']);
            }
            if (isset($data['isAllowBackOrder'])) {
                $customerData->setCustomAttribute('interprise_isallowbackorder', $data['isAllowBackOrder']);
            }
            if (isset($data['isWebAccess'])) {
                $customerData->setCustomAttribute('interprise_iswebaccess', $data['isWebAccess']);
            }
            if (isset($data['classCode'])) {
                $customerData->setCustomAttribute('interprise_classcode', $data['classCode']);
            }

            $vatExempt = false;
            if (isset($data['customFields'])) {
                foreach ($data['customFields'] as $customData) {
                    if (strtolower($customData['field']) == 'vatexempt_c' && strtolower($customData['value']) == true) {
                        $vatExempt = true;
                        break;
                    }
                }

                if ($vatExempt) {
                    foreach ($data['customFields'] as $customData) {
                        if (strtolower($customData['field']) == 'vatexemptexpirydate_c') {
                            if ($customData['value'] != '' && $customData['value'] != null) {
                                $customerData->setCustomAttribute('vatexemptexpirydate_c', trim($customData['value']));
                            } else {
                                $customerData->setCustomAttribute('vatexemptexpirydate_c', '2099-01-01 00:00:00');
                            }
                            break;
                        }
                    }
                }
            }

            $customerData->setCustomAttribute('interprise_apicreated', 1);
            //$update_customer->setPassword('123456');
            $update_customer->setInterpriseApicreated(1);
            $update_customer->updateData($customerData);

            $update_customer->save();

            $customer_id = $update_customer->getId();
            $is_customer_code = $update_customer->getInterpriseCustomerCode();
            $this->makeBillingAddress($data, $customer_id);
            //$this->makAddresses($is_customer_code, $customer_id);

            if ($update_customer->getWebsiteId() != 1) {
                $this->updateAddresses($is_customer_code, $customer_id);
            }
            $status['Status'] = true;
            $status['error'] = '';
            $status['entity_id'] = $customer_id;

            return $status;
        } catch (\Magento\Framework\Exception\AlreadyExistsException $e) {
            print_r([$e->getMessage()]);
            $err_msg = $e->getMessage();
            $status['Status'] = false;
            $status['error'] = "Err msg - " . $err_msg . " in function " . __METHOD__;
            $status['entity_id'] = '';
            $content = [];
            $content['EmailID'] = $data['email'];
            $content['CustomerCode'] = $data['customerCode'];
            $content['Msg'] = "Unable to sync customer because '" . $data['email'] . "' email address is already assigned to another customer. Add a valid, unique email address in interprise.";;
            $this->reattemptorder->sendmessage(self::XML_PATH_TRANSACTION_INVALID_CUSTOMER_EMAILID, $content);

            return $status;
        } catch (Exception $e) {
            print_r([$e->getMessage()]);
            $err_msg = $e->getMessage();
            $status['Status'] = false;
            $status['error'] = "Err msg - " . $err_msg . " in function " . __METHOD__;
            $status['entity_id'] = '';

            return $status;
        }
    }

    public function checkEmailIDExistance($customer_code, $email_id)
    {
        //echo "<br/>".$customer_code;
        //echo "<br/>".$email_id;
        $customercollection = $this->customerFactory->create()->getCollection()
            ->addAttributeToSelect("*")
            ->addAttributeToFilter("interprise_customer_code", ["neq" => $customer_code])
            ->addAttributeToFilter("email", ["eq" => $email_id])
            ->load();
        //echo "<br/>".$customercollection->count();
        if ($customercollection->count() > 0) {
            return false;
        }

        return true;
    }

    public function updateAddresses($customer_code, $magento_customer_id)
    {
        $api_responsc = $this->getCurlData('customer/shipto?customercode=' . $customer_code);
        $message_result = '';
        $delete_shipto_address_array = [];//that shiptos which are not more active in Interprise

        if ($api_responsc['api_error']) {
            $data_shipto = $api_responsc['results']['data'];
            echo '<br/>count($data_shipto) ' . count($data_shipto);
            if (count($data_shipto) > 0) {
                foreach ($data_shipto as $key_shipto => $value_shipto) {
                    $attributes_shipto = $value_shipto['attributes'];
                    if (trim(strtolower($attributes_shipto['shippingMethod'])) == 'rm-free') {
                        if (!$attributes_shipto['isActive']) {/*if disabled in Interprise will delete this address in
                    }
     Magento also*/
                            $address_id = '';

                            echo '<br/>delete_shipto_address ' . $delete_shipto_address = $attributes_shipto['shipToCode'];
                            $address_id = $this->checkShippingAddressExist($delete_shipto_address);
                            if ($address_id == '' || !isset($address_id)) {
                                $a = '1';
                            } else {
                                $address = $this->_addressFactory->create();
                                $address->load($address_id);
                                $address->delete();
                            }
                        } else {
                            $shiptoname = $attributes_shipto['shipToName'];
                            $name_shipto_arr = $this->makeCustomerNameWihCompany($shiptoname);
                            $first_name = $name_shipto_arr['first_name'];
                            $last_name = $name_shipto_arr['last_name'];
                            if ($last_name == '') {
                                $last_name = $first_name;
                            }
                            $company = $name_shipto_arr['company'];
                            if (isset($attributes_shipto['postalCode'])) {
                                $postal_code = $attributes_shipto['postalCode'];
                            } else {
                                $postal_code = 'NA';
                            }
                            $city = '';
                            if (isset($attributes_shipto['city'])) {
                                $city = $attributes_shipto['city'];
                            } else {
                                $city = "NA";
                            }
                            $region = '';
                            if (isset($attributes_shipto['county'])) {
                                $region = $attributes_shipto['county'];
                            }

                            $countrystate = '';
                            if (isset($attributes_shipto['state'])) {
                                $countrystate = $attributes_shipto['state'];
                            }

                            if (isset($attributes_shipto['telephone']) && $attributes_shipto['telephone'] != '') {
                                $telephone = $attributes_shipto['telephone'];
                            } else {
                                $telephone = 9999999999;
                            }
                            $telephone = preg_replace('/\s+/', '', (string)$telephone);

                            if (isset($attributes_shipto['address'])) {
                                $address = $attributes_shipto['address'];
                            } else {
                                $address = 'NA';
                            }

                            $my_string = preg_replace(['/\r\n/'], '#PH', $address);
                            $exploded = explode('#PH', $my_string);
                            $full_address = '';
                            $first_line = '';
                            $second_line = '';
                            $first_line = trim($exploded[0]);
                            if (isset($exploded[1]) && $exploded[1] != '') {
                                $second_line = "\r\n" . trim($exploded[1]);
                            }
                            if (isset($exploded[2]) && $exploded[2] != '') {
                                $second_line .= "," . trim($exploded[2]);
                            }
                            $address_for_magento = $first_line . $second_line;
                            $country_name = $attributes_shipto['country'];
                            $country_code = $this->getISOCodeCountryByName($country_name);
                            $shipto_exist = false;
                            $shipto_data = [];
                            $shipto_data['shiptoCode'] = $attributes_shipto['shipToCode'];
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
                            $shipto_data['interprise_shippingmethodgroup'] = $attributes_shipto['shippingMethodGroup'];
                            $shipto_data['interprise_shippingmethod'] = $attributes_shipto['shippingMethod'];
                            $shipto_data['interprise_freighttax'] = $attributes_shipto['taxCode'];
                            $shipto_process = $this->processShipto($shipto_data, $magento_customer_id);
                            if ($shipto_process['Status']) {
                                $message_result = '';
                            } else {
                                $message_result = $attributes_shipto['shipToCode'] . ' creation failed';
                            }
                        }
                    }
                }
            }
        }
    }
}
