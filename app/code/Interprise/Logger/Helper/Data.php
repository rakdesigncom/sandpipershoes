<?php

namespace Interprise\Logger\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;

/**
 * Class Data
 *
 * @package Interprise\Logger\Helper
 */
class Data extends AbstractHelper
{
    const PHASE_OUT_STATUS = 'P';

    protected $_product;

    protected $_curl;

    protected $_datetime;

    protected $categorycollection;

    protected $productCollectionFactory;

    protected $categoryfactory;

    protected $pricingcustomerfact;

    protected $pricinglistfact;

    protected $productfactory;

    protected $session;

    protected $classmapping;

    protected $_statementaccountFactory;

    protected $_addressFactory;

    protected $_custompayment;

    protected $_custompaymentitem;

    protected $_paymentmethodfact;

    protected $_installwizardFactory;

    protected $_shippingstoreinterpriseFactory;

    protected $_curlFactory;

    protected $api_username;

    protected $api_url;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    private $httpContext;

    private $result_data;

    private $api_responce;

    private $request_url;

    private $reattempt;

    private $is_status;

    private $is_api_key;

    private $is_api_url;

    private $is_next;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Catalog\Model\ProductFactory $product
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $datetime
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categorycollection
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\CategoryFactory $categoryobj
     * @param \Interprise\Logger\Model\PricingcustomerFactory $pricingcustomer
     * @param \Interprise\Logger\Model\PricelistsFactory $pricelistsFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Customer\Model\Session $session
     * @param \Interprise\Logger\Model\CountryclassmappingFactory $classmapping
     * @param \Interprise\Logger\Model\StatementaccountFactory $statementaccountFactory
     * @param \Magento\Customer\Model\AddressFactory $addressFactory
     * @param \Interprise\Logger\Model\CustompaymentFactory $custompaymentFactory
     * @param \Interprise\Logger\Model\CustompaymentitemFactory $custompaymentitemFactory
     * @param \Interprise\Logger\Model\PaymentmethodFactory $paymentmethodfact
     * @param \Interprise\Logger\Model\InstallwizardFactory $installwizardFactory
     * @param \Interprise\Logger\Model\ShippingstoreinterpriseFactory $shippingstoreinterpriseFactory
     */
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
        \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory
    ) {
        $this->_product = $product;
        $this->_curl = $curl;
        $this->_datetime = $datetime;
        $this->categorycollection = $categorycollection;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->categoryfactory = $categoryobj;
        $this->pricingcustomerfact = $pricingcustomer;
        $this->pricinglistfact = $pricelistsFactory;
        $this->productfactory = $productFactory;
        $this->session = $session;
        $this->classmapping = $classmapping;
        $this->_statementaccountFactory = $statementaccountFactory;
        $this->_addressFactory = $addressFactory;
        $this->_custompayment = $custompaymentFactory;
        $this->_custompaymentitem = $custompaymentitemFactory;
        $this->_paymentmethodfact = $paymentmethodfact;
        $this->_installwizardFactory = $installwizardFactory;
        $this->_shippingstoreinterpriseFactory = $shippingstoreinterpriseFactory;
        $this->_curlFactory = $curlFactory;

        parent::__construct($context);
        $this->httpContext = $httpContext;
    }

    /**
     * @param $item_type
     * @param $from
     * @param $to
     *
     * @return array
     */
    public function getChangeLog($item_type, $from, $to)
    {
        $urls = "changelog/" . $item_type . "/" . '?from=' . $from . '&to=' . $to;

        return $this->getCurlData($urls);
    }

    /**
     * @param $URL
     * @param string $full
     *
     * @return array
     */
    public function getCurlData($URL, $full = '')
    {
        $this->result_data = [];
        $this->api_responce = [];
        $this->request_url = [];
        $this->reattempt = true;
        $interprise_status = $this->getConfig('setup/general/enable');
        $interprise_api_key = $this->getConfig('setup/general/api_key');
        $interprise_api_url = $this->getConfig('setup/general/api_url');
        $this->is_status = $interprise_status;
        $this->is_api_key = $interprise_api_key;
        $this->is_api_url = $interprise_api_url;
        if ($full) {
            $URL = $full;
        } else {
            $URL = $this->is_api_url . $URL;
        }
        $this->is_next = false;

        $this->getCurlDataRequest('', $URL);

        return $this->apiResponceData();
    }

    /**
     * @param $config_path
     *
     * @return mixed
     */
    public function getConfig($config_path, $store = null)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param $URL
     * @param string $full
     */
    public function getCurlDataRequest($URL, $full = '')
    {
        $username = $this->is_api_key;
        $password = '';
        if ($full) {
            $URL = $full;
        } else {
            $URL = $this->is_api_url . $URL;
        }

        //echo '<br/>$URL'.$URL = urldecode($URL);
        $URL = str_replace(" ", "%20", $URL);
        try {
            //$interprise_status = $this->getConfig('setup/general/enable');
            $interprise_api_key = $this->getConfig('setup/general/api_key');
            //$interprise_api_url = $this->getConfig('setup/general/api_url');
            $username = $interprise_api_key;
            $password = '';
            $headers = ["Content-Type" => "application/json"];
            $this->_curl->setHeaders($headers);
            $this->_curl->setCredentials($username, $password);
            $this->_curl->setOption(CURLOPT_RETURNTRANSFER, true);
            $this->_curl->get($URL);
            $result = $this->_curl->getBody();
//            print_r([
//                'url' => $URL,
//                'username' => $username,
//                'password' => $password,
//                'headers' => $headers,
//                'result' => $result
//            ]); exit;

            $result = json_decode($result, true);
            $success_status = $this->_curl->getStatus();

            if ($success_status == 200) {
                if (isset($result['links']) && array_key_exists('next', $result['links'])) {
                    $this->is_next = true;
                    $this->getCurlDataRequest('', $result['links']['next']);
                }
                if (isset($result['data']) && array_key_exists('type', $result['data'])) {
                    foreach ($result['data'] as $key => $value) {
                        $this->result_data[$key] = $value;
                    }
                } else {
                    foreach ($result['data'] as $key => $value) {
                        $this->result_data[] = $value;
                    }
                }
                $this->api_responce[] = 'success';
            } else if ($success_status == 404) {
                $this->api_responce[] = 'error';

                if ($result == '') {
                    if ($this->reattempt) {
                        $this->reattempt = false;
                        $this->getCurlDataRequest('', $URL);
                    } else {
                        if (!$this->is_next) {
                            $this->result_data[] = $result;
                        }
                    }
                } else {
                    if (!$this->is_next) {
                        $this->result_data[] = $result;
                    }
                }
            } else if ($success_status == 0) {
                $this->api_responce[] = 'error';
                $this->result_data[] = ['error' => "Server not responding"];
            } else {
                $this->api_responce[] = 'error';
                $this->result_data[] = $result;
            }
            $this->request_url[] = $URL;
        } catch (Exception $e) {
            //$e->getMessage();
            $this->api_responce[] = 'error';
            $this->result_data[] = ['error' => $e->getMessage()];
        }
    }

    /**
     * @return array
     */
    public function apiResponceData()
    {
        $api_status = array_unique($this->api_responce);
        $is_responce = true;
        if (in_array('error', $api_status)) {
            $is_responce = false;
        }
        if ($this->is_next) {
            $is_responce = true;
        }
        $decode_responce = json_encode($this->request_url);

        if ($is_responce) {
            $result = [
                'api_error' => '1',
                'status' => '1',
                'results' => ['data' => $this->result_data],
                'request' => $decode_responce,
            ];

            return $result;
        } else {
            if ($this->result_data[0] === null) {
                $response = "The resource cannot be found";
            } else {
                $response = json_encode($this->result_data);
            }

            return ['api_error' => '0', 'results' => $response, 'request' => $decode_responce];
        }
    }

    /**
     * @param $pushType
     * @param $data_string
     *
     * @return array
     */
    public function putCurlData1($pushType, $data_string)
    {
        $interprise_status = $this->getConfig('setup/general/enable');
        //  $interprise_api_key = $this->getConfig('setup/general/api_key');
        // // $interprise_api_url = $this->getConfig('setup/general/api_url');
        //  $URL = $interprise_api_url . $pushType;
        //   $username = $interprise_api_key;
        //  $password = '';
        //echo "<br><br>$URL -- $username --- p $password p";
        //  $ch = curl_init($URL);
        //   curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        //    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        //   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //   curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        //    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        //        'Content-Type: application/json',
        ////        'Content-Length: ' . strlen($data_string)]);
        //    $result = curl_exec($ch);
        //   $status_code = curl_getinfo($ch);   //get status code
        //   curl_close($ch);

        //    $get_api_result = true;
        ///   if ($status_code['http_code'] == 404) {
        //      $result = json_encode(['errors' => "Resource not found"]);
        //       $get_api_result = false;
        //   } elseif ($status_code['http_code'] == 0) {
        //       $result = json_encode(['errors' => "Server not Responding"]);
        //       $get_api_result = false;
        //  }
        //  if ($get_api_result) {
        //      return ['api_error' => 1, 'result' => $result, 'request' => $URL];
        //   } else {
        //       return ['api_error' => 0, 'result' => $result, 'request' => $URL];
        //   }
    }

    public function putCurlData($pushType, $data_string)
    {
        $interprise_status = $this->getConfig('setup/general/enable');
        $interprise_api_key = $this->getConfig('setup/general/api_key');
        $interprise_api_url = $this->getConfig('setup/general/api_url');
        $URL = $interprise_api_url . $pushType;
        $username = $interprise_api_key;
        $password = '';
        $URL = $interprise_api_url . $pushType;
        /* Create curl factory */
        $httpAdapter = $this->_curlFactory->create();
        $this->_curl->setCredentials($username, $password);
        //Forth parameter is POST body
        $httpAdapter->write(\Laminas\Http\Request::METHOD_PUT, $URL, '1.1', ["Content-Type:application/json", "Authorization : " . "Basic " . base64_encode($username . ":" . $password)], $data_string);
        $result = $httpAdapter->read();
        $body = \Laminas\Http\Response::fromString($result)->getBody();

        /* convert JSON to Array */
        $response = json_decode($body, true);

        if ($response) {
            return ['api_error' => 1, 'result' => $response, 'request' => $URL];
        }

        return ['api_error' => 0, 'result' => $response, 'request' => $URL];
    }

    /**
     * @param $itemcode
     *
     * @return bool
     */
    public function checkProductExistBySku($itemcode)
    {
        $product = $this->_product->create();
        if ($product->getIdBySku($itemcode)) {
            return $product->getIdBySku($itemcode);
        } else {
            return false;
        }
    }

    /**
     * @param $categroy_name
     * @param $parent_category_id
     * @param $interprise_category_code
     *
     * @return mixed
     */
    public function createProductCategoryMagento($categroy_name, $parent_category_id, $interprise_category_code)
    {
        try {
            $rootCat = $this->categoryfactory->create();
            $category = $this->categoryfactory->create();
            $category->setName(ucfirst($categroy_name));
            $category->setParentId($parent_category_id); // 1: root category.
            $category->setIsActive(1);
            $category->setData('interprise_category_code', $interprise_category_code);
            $url = strtolower($categroy_name);

            $category->setUrlKey($url);
            $category->setPath($rootCat->getPath());
            $category->save();
            $created_category_id = $category->getId();
        } catch (Exception $ex) {
            return false;
        }

        return $created_category_id;
    }

    /**
     * @param $string
     *
     * @return string
     */
    public function createNewAttributeName($string)
    {
        $number = '';
        if (filter_var($string, FILTER_SANITIZE_NUMBER_INT)) {
            $number = "_" . filter_var($string, FILTER_SANITIZE_NUMBER_INT);
        }
        //$strings = preg_replace("/[^A-Za-z?! ]/", "", $string);
        $string = preg_replace('#[^0-9a-z]+#i', '_', strtolower($string));
        //$final_string = "matrix_" . $strings . $number;
        $final_string = $string . $number;

        return strtolower($final_string);
    }

    /**
     * @param $array
     */
    public function prf($array)
    {
        echo "<pre>";
        print_r($array);
        echo "</pre>";

        return 0;
    }

    /**
     * @param $string
     * @param $start
     * @param $end
     *
     * @return bool|string
     */
    public function getStringBetween($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) {
            return '';
        }
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;

        return substr($string, $ini, $len);
    }

    /**
     * @param $address_id
     * @param $update_value
     */
    public function updateShiptoAddressAttribute($address_id, $update_value)
    {
        $shippingAddress = $this->_addressFactory->create()->load($address_id);
        if ($shippingAddress->getId()) {
            $shippingAddress = $this->_addressFactory->create()->load($address_id);
        } else {
            $shippingAddress = $this->_addressFactory->create();
        }
        $shippingAddress->setData('interprise_shiptocode', $update_value);
        $shippingAddress->save();
    }

    /**
     * @param $address_id
     * @param $attribute_code
     * @param $update_value
     */
    public function updateAddressAttribute($address_id, $attribute_code, $update_value)
    {
        $shippingAddress = $this->_addressFactory->create()->load($address_id);
        if ($shippingAddress->getId()) {
            $shippingAddress = $this->_addressFactory->create()->load($address_id);
        } else {
            $shippingAddress = $this->_addressFactory->create();
        }
        $shippingAddress->setData($attribute_code, $update_value);
        $shippingAddress->save();
    }

    /**
     * @param $country
     *
     * @return array
     */
    public function getCountryMappingForInterprise($country)
    {
        $collection = $this->classmapping->create()->getCollection();
        $collection->addFieldToFilter('used_country', ['eq' => $country]);
        //$collection->setPageSize(1)->setCurPage(1);
        if ($collection->count() > 0) {
            $result = $collection->getData();

            return $result[0];
        } else {
            return ['interprise_country' => $country, 'interprise_customer_class' => 'Shopify-WW'];
        }
    }

    /**
     * @return int
     */
    public function getPriceTest()
    {
        return 39;
    }

    /**
     * @param $sku
     * @param $customer_code
     * @param int $qty
     * @param string $currency
     *
     * @return bool
     */
    public function getSpecialCustomerProductPrice($sku, $customer_code, $qty = 1, $currency = 'GBP')
    {
        //$query = "SELECT price as minprice,qty_upto,item_code,(qty_upto-$qty) AS diff,MIN(qty_upto-$qty) AS
        // minvalue  FROM `interprise_pricing_customer`
        //              WHERE customer_code='$customer_code' AND item_code='$sku' AND currency='$currency' AND
        //(qty_upto-$qty)>0";
        //$result = $connection->fetchRow($query);
        $collection1 = $this->pricingcustomerfact->create();
        $collection1->getCollection();
        $collection1->addFieldToFilter('customer_code', ['eq' => $customer_code]);
        $collection1->addFieldToFilter('item_code', ['eq' => $sku]);
        $collection1->addFieldToFilter('currency', ['eq' => $currency]);
        $collection1->addFieldToFilter('qty_upto', ['gt' => 0]);
        $collection1->setPageSize(1)->setCurPage(1);
        if ($collection1->count() > 0) {
            $result = $collection1->getFirstItem();
        }
        if ($result) {
            if ($result['price'] != '') {
                return $result['price'];
            } else {
                $flag_process = true;
            }
        } else {
            $flag_process = true;
        }
        //$flag_process = true;
        $customerSession = $this->session->create();
        if ($flag_process) {
            if ($customerSession->isLoggedIn()) {
                $pricing_level = $customerSession->getData('interprise_pricinglevel');
                $collection2 = $this->pricinglistfact->create();
                $collection2->getCollection();
                $collection2->addFieldToFilter('pricelist', ['eq' => $pricing_level]);
                $collection2->addFieldToFilter('itemcode', ['eq' => $sku]);
                $collection2->addFieldToFilter('unitofmeasure', ['eq' => 'EACH']);
                $collection2->addFieldToFilter('currency', ['eq' => $currency]);
                if ($collection2->count() > 0) {
                    $result2 = $collection2->getFirstItem();
                }
                if ($result2) {
                    if ($result2['price'] != '') {
                        return $result2['price'];
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            }

            return false;
        }
    }

    /**
     * @param $itme_code
     * @param string $_currency_code
     *
     * @return bool
     */
    public function interprisePriceLists($itme_code, $_currency_code = 'GBP')
    {
        if ($itme_code == '') {
            return false;
        }
        $collection2 = $this->pricinglistfact->create();
        $collection2->getCollection();
        $collection2->addFieldToFilter('itemcode', ['eq' => $itme_code]);
        $collection2->addFieldToFilter('currency', ['eq' => $_currency_code]);
        if ($collection2->count() > 0) {
            foreach ($collection2 as $collect) {
                $collect->delete();
            }
        }
        if ($_currency_code == 'EUR') {
            $_currency_code = 'EURO';
        }
        $price_lists = 'pricing/pricelist/detail?itemCode=' . $itme_code . "&Currency=" . $_currency_code;
        $price_lists_api_result = $this->getCurlData($price_lists);
        if ($price_lists_api_result['api_error']) {
            $result = $price_lists_api_result['results']['data'];
            if (count($result) > 0) {
                foreach ($result as $key => $value) {
                    $arribute_price = $value['attributes'];
                    $itemcode = $arribute_price['itemCode'];
                    $pricelist = $arribute_price['pricingLevel'];
                    $currency = '';
                    if ($arribute_price['currencyCode'] == 'EURO') {
                        $currency = 'EUR';
                    } else {
                        $currency = $arribute_price['currencyCode'];
                    }
                    $price = $arribute_price['salesPrice'];
                    $from_qty = $arribute_price['minQuantity'];
                    $to_qty = $arribute_price['maxQuantity'];
                    $price_fact = $this->pricinglistfact->create();
                    $price_fact->setData('itemcode', $itemcode);
                    $price_fact->setData('pricelist', $pricelist);
                    $price_fact->setData('price', $price);
                    $price_fact->setData('from_qty', $from_qty);
                    $price_fact->setData('to_qty', $to_qty);
                    $price_fact->setData('currency', $currency);
                    $price_fact->save();
                }
            }
        }
        $status['Status'] = true;
        $status['error'] = '';
        $status['entity_id'] = '';

        return $status;
    }

    /**
     * @param $customer_code
     *
     * @return mixed
     */
    public function updateCustomerStatement($customer_code)
    {
        $magento_customer_id = $this->isCustomerExistByInterpriseCode($customer_code);
        if (!$magento_customer_id) {
            $error_message = "$customer_code not exist in magento so unable
                                to update customer account statemnt in table";
            $status['Status'] = false;
            $status['error'] = $error_message;
            $status['entity_id'] = '';

            return $status;
        }
        $collection_del = $this->_statementaccountFactory->create()->getCollection();
        $collection_del->addFieldToFilter('customer_id', ['eq' => $magento_customer_id]);
        if ($collection_del->count() > 0) {
            foreach ($collection_del as $item_del) {
                $item_del->delete();
            }
        }
        $api_responsc = $this->getCurlData("customer/statementofaccount?customercode=$customer_code");
        $data = $api_responsc['results']['data'];
        foreach ($data as $key => $value) {
            $statement_create = $this->_statementaccountFactory->create();
            $statement_create->setData('customer_id', $magento_customer_id);
            $statement_create->setData('invoice_code', $value['attributes']['invoiceCode']);
            $statement_create->setData('document_date', $value['attributes']['documentDate']);
            $statement_create->setData('due_date', $value['attributes']['dueDate']);
            $statement_create->setData('document_type', $value['attributes']['documentType']);
            $statement_create->setData('balance_rate', $value['attributes']['balanceRate']);
            $statement_create->setData('total_rate', $value['attributes']['totalRate']);
            if (isset($value['attributes']['reference'])) {
                $statement_create->setData('reference', $value['attributes']['reference']);
            }
            $statement_create->save();
        }
        $status['Status'] = true;
        $status['error'] = "";
        $status['entity_id'] = $magento_customer_id;

        return $status;
    }

    /**
     * @param $pay_model
     * @param $customer_code
     */
    public function postReciptsInIs($pay_model, $customer_code)
    {
        $payment_id = $pay_model['entity_id'];
        $_receipt = [];
        $_receipt['customercode'] = $customer_code;
        $_receipt['paymentType'] = 'Cash/Other';
        $_receipt['amountPaid'] = $pay_model['amount'];
        $_receipt['notes'] = 'receipt';
        $_receipt['isDeposit'] = true;
        $_receipt['bankAccount'] = 'BNKAC-000001';

        $_receipt_responce = $this->postCurlData('customer/receipt', json_encode($_receipt));
        if ($_receipt_responce['api_error']) {
            $responc_result = "RCV" . filter_var($_receipt_responce['result'], FILTER_SANITIZE_NUMBER_INT);
            $paymentfact = $this->_custompayment->create()->load($payment_id);
            $paymentfact->setData('is_receipt_no', $responc_result);
            $paymentfact->setData('receipt_status', 'success');
            $paymentfact->save();
            $this->postReceiptAllocationInvoices(
                $payment_id,
                $responc_result,
                $customer_code,
                $pay_model['amount'],
                $pay_model
            );
        } else {
            $paymentfact = $this->_custompayment->create()->load($payment_id);
            $paymentfact->setData('receipt_status', 'fail');
            $paymentfact->save();
        }
    }

    /**
     * @param $pushType
     * @param $data_string
     *
     * @return array
     */
    public function postCurlData($pushType, $data_string)
    {
        $interprise_status = $this->getConfig('setup/general/enable');
        $interprise_api_key = $this->getConfig('setup/general/api_key');
        $interprise_api_url = $this->getConfig('setup/general/api_url');
        $this->is_status = $interprise_status;
        $this->api_username = $interprise_api_key;
        $this->api_url = $interprise_api_url;

        $URL = $this->api_url . $pushType;
        $username = $this->api_username;
        $password = '';
        $headers = ["Content-Type" => "application/json", "Content-Length" => strlen($data_string)];
        $this->_curl->setHeaders($headers);
        $this->_curl->setCredentials($username, $password);
        $this->_curl->setOption(CURLOPT_RETURNTRANSFER, true);
        $this->_curl->post($URL, $data_string);
        $response = $this->_curl->getBody();
        $success_status = $this->_curl->getStatus();
        if ($success_status == 404) {
            return ['api_error' => 1, 'result' => $response, 'request' => $URL];
        } else {
            return ['api_error' => 0, 'result' => $response, 'request' => $URL];
        }
    }

    /**
     * @param $payment_id
     * @param $receipt_id
     * @param $customerCode
     * @param $allocatedAmount
     * @param $pay_model
     */
    public function postReceiptAllocationInvoices($payment_id, $receipt_id, $customerCode, $allocatedAmount, $pay_model)
    {
        $paymentitemcollection = $this->_custompaymentitem->create();
        $paymentitemcollection->getCollection();
        $paymentitemcollection->addFieldToFilter('payment_id', ['eq' => $payment_id]);
        $result = $paymentitemcollection;
        if ($result) {
            $_allocation_arr = [];
            $_allocation_arr['customerCode'] = $customerCode;
            $_allocation_arr['receivableCode'] = $receipt_id;
            $_allocation_arr['allocatedAmount'] = $allocatedAmount;
            $_allocation_items = [];
            foreach ($result as $_item) {
                $_allocation_items[] = ['documentCode' => $_item['itme_code'], 'allocatedAmount' => $_item['amount']];
            }
            $_allocation_arr['debits'] = $_allocation_items;
            $allocation_string = json_encode($_allocation_arr);
            $_receipt_allocation_responce = $this->postCurlData('customer/receipt/allocation', $allocation_string);
            if ($_receipt_allocation_responce['api_error']) {
                $paymentfact = $this->_custompayment->create()->load($payment_id);
                $paymentfact->setData('receipt_allocation_status', 'success');
                $paymentfact->save();
            } else {
                $paymentfact = $this->_custompayment->create()->load($payment_id);
                $paymentfact->setData('receipt_allocation_status', 'fail');
                $paymentfact->save();
            }
        }
    }

    /**
     * @return array|bool
     */
    public function insertPaymentMethods()
    {
        $paytem = 'system/paymentterm';
        $paytem_result = $this->getCurlData($paytem);
        $searchArray = [];
        $count_inserted = 0;
        $data = $paytem_result['results']['data'];
        if (count($data) > 0) {
            foreach ($data as $k => $value) {
                $attributes = $value['attributes'];
                $payment_term_code = $attributes['paymentTermCode'];
                $payment_term_type = $attributes['paymentType'];
                $main_key = $payment_term_code . '@' . $payment_term_type;
                $searchArray[$main_key] = $attributes['paymentMethodCode'];
            }
        }
        $collection_paymentmethod = $this->_paymentmethodfact->create();
        $collection_paymentmethod->getCollection();
        foreach ($collection_paymentmethod as $itempayment) {
            $itempayment->delete();
        }
        $paymentTerm = 'system/paymenttermgroup';
        $paymentTerm_api_result = $this->getCurlData($paymentTerm);
        if ($paymentTerm_api_result['api_error']) {
            $result = $paymentTerm_api_result['results']['data'];
            if (count($result) > 0) {
                foreach ($result as $key => $_payment) {
                    $payment_attribute = $_payment['attributes'];
                    $paymentTermGroup = $payment_attribute['paymentTermGroup'];
                    $paymentTermCode = $payment_attribute['paymentTermCode'];
                    $paymentType = $payment_attribute['paymentType'];
                    $paymentTermDescription = $payment_attribute['paymentTermDescription'];
                    $isActive = $payment_attribute['isActive'];
                    //$default_payment_method = $payment_attribute['defaultPaymentMethod'];
                    $key_ptm = $paymentTermCode . '@' . $paymentType;
                    $default_payment_method = $searchArray[$key_ptm];
                    $count_inserted++;
                    $model_paymentmethod = $this->_paymentmethodfact->create();
                    $model_paymentmethod->setData('is_payment_term_group', $paymentTermGroup);
                    $model_paymentmethod->setData('is_payment_term_code', $paymentTermCode);
                    $model_paymentmethod->setData('is_payment_type', $paymentType);
                    $model_paymentmethod->setData('is_payment_term_description', $paymentTermDescription);
                    $model_paymentmethod->setData('is_isactive', $isActive);
                    $model_paymentmethod->setData('default_payment_method', $default_payment_method);
                    if ($default_payment_method == 'Credit Card') {
                        $model_paymentmethod->setData('magento_method', 'magenest_sagepay');
                    } else {
                        $model_paymentmethod->setData('magento_method', 'custompaymentmethod');
                    }
                    $model_paymentmethod->save();
                }
            }
            unset($searchArray);
        } else {
            return false;
        }
        $collect = $this->_installwizardFactory->create();
        $collect->addFieldToFilter('function_name', ['eq' => 'payment_methods']);
        foreach ($collect as $colitem) {
            $colitem->setData('status', 'Done');
            $colitem->setData('sync_done', '1');
            $colitem->setData('action', 'Done');
            $colitem->setData('total_records', $count_inserted);
        }
        $collect->save();

        return ['status' => true];
    }

    /**
     * @return array
     */
    public function insertShippingMethods()
    {
        $paytem = 'shippingmethod/shippingmethodgroup';
        $paytem_result = $this->getCurlData($paytem);
        $searchArray = [];
        $count_inserted = 0;
        $data = $paytem_result['results']['data'];
        $replace_query = "";
        if (count($data) > 0) {
            $shipping_collect = $this->_shippingstoreinterpriseFactory->create();
            $shipping_collect->getCollection();
            foreach ($shipping_collect as $shItem) {
                $shItem->delete();
            }
            foreach ($data as $k => $value) {
                $attributes = $value['attributes'];
                $shippingMethodCode = $attributes['shippingMethodCode'];
                $shipping_model = $this->_shippingstoreinterpriseFactory->create();
                $shipping_model->setData('interprise_shipping_method_code', $shippingMethodCode);
                $shipping_model->save();
                $count_inserted++;
            }
        }
        $collect = $this->_installwizardFactory->create();
        $collect->addFieldToFilter('function_name', ['eq' => 'shipping_methods']);
        foreach ($collect as $colitem) {
            $colitem->setData('status', 'Done');
            $colitem->setData('sync_done', '1');
            $colitem->setData('action', 'Done');
            $colitem->setData('total_records', $count_inserted);
        }
        $collect->save();

        return ['status' => true];
    }

    /**
     * @return string
     */
    public function populateCountryClassTable()
    {
        $country = 'system/country';
        $country_api_result = $this->getCurlData($country);
        if (!$country_api_result['api_error']) {
            return 'fail';
        }
        $country_results = $country_api_result['results']['data'];
        if (count($country_results) > 0) {
            $all_collection = $this->classmapping->create()->getCollection();
            foreach ($all_collection as $_itemcol) {
                $_itemcol->delete();
            }
            foreach ($country_results as $k => $v_results) {
                $attributes = $v_results['attributes'];
                $countryobj = $this->classmapping->create();
                $countryobj->setData('iso_code', $attributes['isoCode']);
                $countryobj->setData('used_country', $attributes['countryCode']);
                $countryobj->setData('interprise_country', $attributes['countryCode']);
                $countryobj->setData(
                    'interprise_customer_class',
                    $attributes['defaultRetailCustomerBillToClassTemplate']
                );
                $countryobj->setData(
                    'interprise_shipto_class',
                    $attributes['defaultRetailCustomerShipToClassTemplate']
                );
                $countryobj->save();
            }
        }

        return count($country_results);
    }

    /**
     *
     */
    public function createCategorystructure()
    {
        $paytem = 'system/category/structure';
        $paytem_result = $this->getCurlData($paytem);
        $searchArray = [];
        $count_inserted = 0;
        $data = $paytem_result['results']['data'];
        if (count($data) > 0) {
            foreach ($data as $k => $value) {
                $attributes = $value['attributes'];
                $searchArray[] = ['id' => $attributes['categoryCode'], 'parent_id' => $attributes['parentCategory']];
            }
        }
        $childs = [];
        $items = $searchArray;
        foreach ($items as &$item) {
            $childs[$item['parent_id']][] = &$item;
        }
        unset($item);

        foreach ($items as &$item) {
            if (isset($childs[$item['id']])) {
                $item['childs'] = $childs[$item['id']];
            }
        }
        unset($item);

        $tree = $childs['DEFAULT'];
        $this->makearr($tree, 4);
    }

    /**
     * @param $tree
     * @param $parent_id
     */
    public function makearr($tree, $parent_id)
    {
        foreach ($tree as $kt => $valt) {
            if (strtolower($valt['id']) == 'default') {
                continue;
            }
            $parent_ids = $this->createCategories($valt['id'], $parent_id);
            if (isset($valt['childs']) && is_array($valt['childs']) && count($valt['childs']) > 0) {
                $this->makearr($valt['childs'], $parent_ids);
            }
        }
    }

    /**
     * @param $categoryname
     * @param $parent_id
     *
     * @return mixed
     */
    public function createCategories($categoryname, $parent_id)
    {
        $category = $this->categoryfactory->create();
        $category->setName($categoryname);
        $category->setParentId($parent_id); // 1: root category.
        $category->setIsActive(1);
        $category->setUrlKey(strtolower($categoryname . rand(10, 999)));
        $category->setCustomAttributes([
            'description' => $categoryname,
            'interprise_category_code' => $categoryname,
        ]);
        try {
            //$res = $objectManager->get('\Magento\Catalog\Api\CategoryRepositoryInterface')->save($category);
            $category->save();
        } catch (Exception $ex) {
            return 0;
        }

        return $category->getId();
    }

    /**
     * @param $data
     * @param $sortKey
     * @param int $sort_flags
     *
     * @return array
     */
    public function sortByKeyValue($data, $sortKey, $sort_flags = SORT_ASC)
    {
        if (empty($data) || empty($sortKey)) {
            return $data;
        }

        $ordered = [];
        foreach ($data as $key => $value) {
            $ordered[$value[$sortKey]] = $value;
        }

        ksort($ordered, $sort_flags);

        return array_values($ordered); // array_values() added for identical result with multisort*
    }

    /**
     *
     */
    public function createCategorystructure2()
    {
        $default_cat = $this->getConfig('setup/general/defaultcategory');
        $cat_structure = $this->getarrayfromcurl("system/category/structure");
        $cat_all = $this->getarrayfromcurl("system/category/all");
        $cat_all_key = [];
        foreach ($cat_all as $key => $value) {
            $cat_all_key[$value["categoryCode"]] = $value;
        }
        $cat_str_key = [];
        foreach ($cat_structure as $key => $value) {
            $cat_str_key[$value["categoryCode"]] = $value;
            $cat_str_key[$value["categoryCode"]]['isActive'] = $cat_all_key[$value["categoryCode"]]['isActive'];
        }
        foreach ($cat_str_key as $key => $value) {
            $this->createCat($value, $cat_str_key, $default_cat);
        }
    }

    /**
     * @param $curlurl
     *
     * @return array
     */
    public function getarrayfromcurl($curlurl)
    {
        $curl_json = $this->getCurlData($curlurl);
        if ($curl_json["api_error"]) {
            $data = $curl_json['results']['data'];
            $attribute_array = [];
            if (count($data) > 0) {
                foreach ($data as $k => $value) {
                    $attribute_array[] = $value['attributes'];
                }
            }

            return $attribute_array;
        } else {
            return [];
        }
    }

    /**
     * @param $cat
     * @param $cat_structure
     * @param $default_cat
     */
    public function createCat($cat, $cat_structure, $default_cat)
    {
        if (!$this->checkCategoryExistByIsCode($cat["categoryCode"])) {
            $catcode = $cat["categoryCode"];
            $parent = $cat["parentCategory"];
            $parent_id = $this->checkCategoryExistByIsCode($parent);
            //  echo $catcode."----".$parent."-----".$parent_id."<br>";
            if ($parent == "DEFAULT" || $parent == $catcode) {
                $this->createCategories2($catcode, $cat['description'], $default_cat, $cat['isActive']);
            } else if ($parent_id > 0) {
                $this->createCategories2($catcode, $cat['description'], $parent_id, $cat['isActive']);
            } else if (isset($cat_structure[$parent])) {
                $isactives = $cat_structure[$parent]['isActive'];
                $this->createCat($cat_structure[$parent], $cat_structure, $default_cat, $isactives);
                $parent_id = $this->checkCategoryExistByIsCode($parent);
                $this->createCategories2($catcode, $cat['description'], $parent_id, $cat['isActive']);
            }
        }
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return void
     */
    public function processProductStatus($product)
    {
        if ($product->getInterpriseStatus() != self::PHASE_OUT_STATUS) {
            return;
        }

        $categories = $this->getCategoriesByIds($product->getCategoryIds());
        $newCategories = [];
        foreach ($categories as $category) {
            if ($category->getData('is_category_sale')) {
                $newCategories[$category->getId()] = $category->getId();
            }
        }

        $product->setCategoryIds($newCategories);
        ObjectManager::getInstance()->get(\Magento\Catalog\Api\CategoryLinkManagementInterface::class)->assignProductToCategories(
            $product->getSku(),
            $newCategories
        );
    }

    public function getCategoriesByIds($categoryIds)
    {
        if (!$categoryIds) {
            return [];
        }

        $categorycollection = $this->categorycollection->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id', ['in' => $categoryIds]);

        return $categorycollection;
    }

    /**
     * @param $dataId
     *
     * @return array
     */
    public function checkCategoryExistByIsCode($dataId)
    {
        if (!$dataId) {
            return [];
        }
        $dataId = trim($dataId);

        $categorycollection = $this->categorycollection->create()
            ->addAttributeToSelect(['name', 'image'])
            ->addAttributeToFilter('interprise_category_code', ['eq' => "$dataId"]);
        $data = $categorycollection->getData();

        if (count($data) > 0) {
            return array_column($data, 'entity_id');
        }

        return [];
    }

    /**
     * @param $categorycode
     * @param $categoryname
     * @param $parent_id
     * @param $isactive
     *
     * @return mixed
     */
    public function createCategories2($categorycode, $categoryname, $parent_id, $isactive)
    {
        $category = $this->categoryfactory->create();
        $category->setName($categoryname);
        $category->setParentId($parent_id); // 1: root category.
        $category->setIsActive($isactive);
        echo '<br/>$url' . $url = $this->createurl($categoryname);
        echo '<br/>' . strtolower($url . rand(10, 999));
        $category->setUrlKey(strtolower($url . rand(10, 999)));
        $category->setCustomAttributes([
            'description' => $categoryname,
            'interprise_category_code' => $categorycode,
        ]);
        //try {
        //$res = $objectManager->get('\Magento\Catalog\Api\CategoryRepositoryInterface')->save($category);
        $category->save();
//        } catch (Exception $ex) {
//            return 0;
//        }
        return $category->getId();
    }

    /**
     * @param $category_code
     *
     * @return mixed
     */
    public function createurl($category_code)
    {
        $category_code1 = $category_code;
        $category_code1 = str_replace('&', '_', $category_code1);
        $category_code1 = str_replace('!', '_', $category_code1);
        $category_code1 = str_replace('#', '_', $category_code1);
        $category_code1 = str_replace('$', '_', $category_code1);
        $category_code1 = str_replace("'", '_', $category_code1);
        $category_code1 = str_replace("(", '_', $category_code1);
        $category_code1 = str_replace(")", '_', $category_code1);
        $category_code1 = str_replace("*", '_', $category_code1);
        $category_code1 = str_replace("+", '_', $category_code1);
        $category_code1 = str_replace(",", '_', $category_code1);
        $category_code1 = str_replace("/", '_', $category_code1);
        $category_code1 = str_replace(":", '_', $category_code1);
        $category_code1 = str_replace(";", '_', $category_code1);
        $category_code1 = str_replace("=", '_', $category_code1);
        $category_code1 = str_replace("?", '_', $category_code1);
        $category_code1 = str_replace("@", '_', $category_code1);
        $category_code1 = str_replace("[", '_', $category_code1);
        $category_code1 = str_replace("]", '_', $category_code1);
        $category_code1 = str_replace(" ", '_', $category_code1);
        $category_code1 = strtolower($category_code1);

        return $category_code1;
    }

    public function categoryWebOptionSingle($data)
    {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        ini_set('display_errors', '1');
        echo '<br/>$dataId' . $dataId = $data['DataId'];
        echo '<br/>$default_cat' . $default_cat = $this->getConfig('setup/general/defaultcategory');
        if (!$this->checkCategoryExistByIsCode($dataId)) {
            echo '<br/>$catcode' . $catcode = $dataId;
            //echo '<br/>$catcode'.$parent=$cat["parentCategory"];
            $api_responsc = $this->getCurlData('system/category?categoryCode=' . $catcode);
            echo '<pre>';
            print_r($api_responsc);
            echo '</pre>';
            if ($api_responsc['results']['data'] && $api_responsc['api_error']) {
                echo "Inside This";
                $update_data['ActivityTime'] = $this->getCurrentTime();
                $update_data['Request'] = $api_responsc['request'];
                $update_data['Response'] = json_encode($api_responsc['results']['data']);

                if (!isset($api_responsc['results']['data']['attributes'])) {
                    $update_data['Status'] = 'Fail';
                    $update_data['Remarks'] = 'Attribute data not found';

                    return $update_data;
                }

                $cat = $api_responsc['results']['data']['attributes'];

                echo '<pre>$cat';
                print_r($cat);
                echo '</pre>';
                if (!empty($cat)) {
                    echo '<br/>$parent' . $parent = $cat["parentCategory"];
                    echo '<br/>$parent_id' . $parent_id = $this->checkCategoryExistByIsCode($parent);
                    $api_responsc_desc = $this->getCurlData('system/category/weboption/description?categoryCode=' . $catcode);
                    if ($api_responsc_desc['results']['data'] && $api_responsc_desc['api_error']) {
                        if (!isset($api_responsc_desc['results']['data'])) {
                            $categoryDescription = '';
                        } else {
                            $webSiteCode = 'WEB-000001';
                            foreach ($api_responsc_desc['results']['data'] as $api_catdesc_data) {
                                if ($api_catdesc_data['attributes']['webSiteCode'] == $webSiteCode) {
                                    $categoryDescription = $api_catdesc_data['attributes']['webDescription'];
                                }
                            }
                        }
                        echo '<br/>$categoryDescription ' . $categoryDescription;
                        if ($parent == "DEFAULT" || $parent == $catcode) {
                            echo '<br/>ID ' . $catId = $this->createCategories2($catcode, $catcode, $default_cat, $cat['isActive']);
                        } else if ($parent_id > 0) {
                            echo '<br/>ID ' . $catId = $this->createCategories2('Manisha', 'Manisha', 2, $cat['isActive']);
                            //    echo '<br/>ID '.$catId = $this->createCategories2($catcode, $catcode, $parent_id, $cat['isActive']);
                        }
                    }
                }
            }
            die;
        }
    }

    /**
     * @return mixed
     */
    public function getCurrentTime()
    {
        //$magentoDateObject = $this->_datetime->create();
        return $this->_datetime->gmtDate();
    }

    /**
     * @param $itemcode
     *
     * @return array|void
     */
    public function getUnitmeasuercodeInterprise($itemcode)
    {
        $result_retails = $this->getCurlData("inventory/$itemcode/unitofmeasure");
        if ($result_retails['api_error']) {
            $unitmeasurecode = $result_retails['results']['data'][0]['attributes']['unitMeasureCode'];
            $upccode = '';
            if (isset($result_retails['results']['data'][0]['attributes']['upcCode'])) {
                $upccode = $result_retails['results']['data'][0]['attributes']['upcCode'];
            }

            return ['unitofmeasure' => $unitmeasurecode, 'upccode' => $upccode];
        } else {
            return;
        }
    }

    public function checkDepartmentFromInterprise($itemcode)
    {
        $result_retails = $this->getCurlData("inventory/$itemcode/department");
        if ($result_retails['api_error']) {
            $resule_data = $result_retails['results']['data'];
            $upccode = '';
            if (isset($resule_data)) {
                foreach ($resule_data as $data) {
                    if (strtolower($data['attributes']['departmentCode']) == 'sandpiper') {
                        return true;
                    }
                }
            }

            return false;
        } else {
            return false;
        }
    }

    /**
     * @param $itemcode
     * @param $is_pricinglevel
     * @param string $unitofmeasure
     *
     * @return bool
     */
    public function getLowestTierprice($itemcode, $is_pricinglevel, $unitofmeasure = "EACH")
    {
        //$quuery2 = "SELECT min(price) as minvalue  FROM `interprise_price_lists` WHERE pricelist='$is_pricinglevel'
        // AND itemcode='$itemcode' and unitofmeasure='EACH' AND currency='GBP'";
        //$result2 = $connnections->fetchRow($quuery2);
        $collection2 = $this->pricinglistfact->create();
        $collection2->getCollection();
        $collection2->addFieldToFilter('pricelist', ['eq' => $is_pricinglevel]);
        $collection2->addFieldToFilter('itemcode', ['eq' => $itemcode]);
        $collection2->addFieldToFilter('unitofmeasure', ['eq' => 'EACH']);
        $collection2->addFieldToFilter('currency', ['eq' => 'GBP']);
        if ($collection2->count() > 0) {
            $result2 = $collection2->getFirstItem();
        }
        if ($result2) {
            return $result2['minvalue'];
        } else {
            return false;
        }
    }

    /**
     * @param $itemcode
     * @param string $customercode
     * @param int $qty
     * @param string $currency
     * @param string $unitofmeasure
     *
     * @return float|int|mixed
     */
    public function getSpecialPriceForFrontend(
        $itemcode,
        $customercode = '',
        $qty = 1,
        $currency = 'GBP',
        $unitofmeasure = "EACH"
    ) {
        $magento_product_id = $this->checkProductExistByItemCode($itemcode);
        $set_product = $this->productfactory->create()->load($magento_product_id);
        $final_price_magento = $set_product->getFinalPrice();

        $customerSession = $this->session->create();
        $is_customercode = $customerSession->getISCode();
        if ($customercode != '') {
            $is_customercode = $customercode;
        }
        $is_pricinglevel = $customerSession->getISPricinglevel();
        $is_taxcode = $customerSession->getISTaxcode();
        $is_priceing_mthod = $customerSession->getInterprisepricingmethod();
        $is_defaultprice = $customerSession->getInterpriseDefaultprice();
        if ($is_defaultprice == 'Wholesale') {
            $final_price_magento = $set_product->getData('is_wholesaleprice');
        }
        $interprise_discount_percentage = (int)$customerSession->getInterprisediscount();
        $sku = $itemcode;
        $array_min_cal = [];
        $customer_special_price = '';
        $customer_price_list = '';
        //$query = "SELECT price as minprice,qty_upto,item_code,(qty_upto-$qty) AS diff,MIN(qty_upto-$qty) AS
        // minvalue  FROM `interprise_pricing_customer`
        //WHERE customer_code='$is_customercode' AND item_code='$sku' AND currency='$currency' AND
        //(qty_upto-$qty)>0 limit 1";
        //  $result1 = $connnections->fetchRow($query);
        $collection1 = $this->pricingcustomerfact->create();
        $collection1->getCollection();
        $collection1->addFieldToFilter('customer_code', ['eq' => $is_customercode]);
        $collection1->addFieldToFilter('item_code', ['eq' => $sku]);
        $collection1->addFieldToFilter('currency', ['eq' => $currency]);
        $collection1->addFieldToFilter('qty_upto', ['gt' => 0]);
        $collection1->setPageSize(1)->setCurPage(1);
        if ($collection1->count() > 0) {
            $result1 = $collection1->getFirstItem();
        }

        if ($result1) {
            $interprise_customer_pricing_price = $result1['price'];
            if (isset($interprise_customer_pricing_price) && $interprise_customer_pricing_price > 0) {
                $array_min_cal[] = $interprise_customer_pricing_price;
                $customer_special_price = $interprise_customer_pricing_price;
            }
        }
        $collection2 = $this->pricinglistfact->create();
        $collection2->getCollection();
        $collection2->addFieldToFilter('pricelist', ['eq' => $is_pricinglevel]);
        $collection2->addFieldToFilter('itemcode', ['eq' => $sku]);
        $collection2->addFieldToFilter('unitofmeasure', ['eq' => $unitofmeasure]);
        $collection2->addFieldToFilter('currency', ['eq' => $currency]);
        $collection2->addFieldToFilter('to_qty', ['gt' => 0]);
        $collection2->setPageSize(1)->setCurPage(1);
        if ($collection2->count() > 0) {
            $result2 = $collection2->getFirstItem();
        }

        if ($result2) {
            $interprise_price_lists = $result2['price'];
            if (isset($interprise_price_lists) && $interprise_price_lists != '' && $interprise_price_lists > 0) {
                $array_min_cal[] = $interprise_price_lists;
                $customer_price_list = $interprise_price_lists;
            }
        }
        $calc_min = $final_price_magento;
        if (isset($customer_price_list) && $customer_price_list != '') {
            $calc_min = min($calc_min, $customer_price_list);
        }
        if (isset($interprise_discount_percentage) && $interprise_discount_percentage > 0) {
            $calc_min = (1 - $interprise_discount_percentage / 100) * $calc_min;
        }
        if (isset($customer_special_price) && $customer_special_price != '') {
            $calc_min = min($calc_min, $customer_special_price);
        }

        return $calc_min;
    }

    public function updateProductStock($product_id, $stock_availability)
    {
        try {
            $erpInventory = ObjectManager::getInstance()->get(\Interprise\Logger\Model\Erp\Inventory::class);
            $erpInventory->updateProductStock($product_id, $stock_availability);

            $status['Status'] = true;
            $status['error'] = "";
            $status['entity_id'] = $product_id;

            return $status;
        } catch (Exception $ex) {
            $err_msg = json_encode($ex->getMessage());
            $status['Status'] = false;
            $status['error'] = "Err msg - " . $err_msg . " in function " . __METHOD__;
            $status['entity_id'] = '';

            return $status;
        }
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function updateWhosalePrice($initialProduct)
    {
        if ($initialProduct->getTypeId() == 'configurable' || $initialProduct->getData('interprise_item_type') == 'Matrix Group') {
            return;
        }

        $whoSalePrice = $initialProduct->getData('is_wholesaleprice');
        if (!$whoSalePrice) {
            $whoSalePrice = $initialProduct->getData('is_retailprice');
        }

        if ($whoSalePrice <= 0) {
            return;
        }

        $product = $this->productfactory->create()->setStoreId(0)->load($initialProduct->getId());
        if (!$product->getId()) {
            return;
        }

        try {
            /** @var \Magento\Catalog\Api\Data\ProductTierPriceInterface $tierPriceData */
            $tierPriceData = ObjectManager::getInstance()->create(\Magento\Catalog\Api\Data\ProductTierPriceInterfaceFactory::class)->create();
            $tierPriceData->setCustomerGroupId(2)
                ->setQty(1.0)
                ->setValue((float)$whoSalePrice);
            $extensionAttributes = $tierPriceData->getExtensionAttributes();
            $extensionAttributes->setWebsiteId(0);
            $tierPriceData->setExtensionAttributes($extensionAttributes);

            $product->setTierPrices([$tierPriceData]);
            $product->setSpecialPrice(null)->setSpecialFromDate(null)->setSpecialToDate(null);
            $product->save();

            if ($product->getData('is_specialprice')) {
                $specialPrice = json_decode($product->getData('is_specialprice'), true);
                if (!empty($specialPrice['date_from'])) {
                    $specialPrice['date_from'] = date('Y-m-d H:i:s', strtotime($specialPrice['date_from']));
                }
                if (!empty($specialPrice['date_to'])) {
                    $specialPrice['date_to'] = date('Y-m-d H:i:s', strtotime($specialPrice['date_to']));
                }
            } else {
                $specialPrice = [];
            }

            $repository = ObjectManager::getInstance()->get(\Magento\Catalog\Model\ProductRepository::class);
            $productNew = $repository->getById($product->getId(), true, 1, true);
            $productNew->setSpecialPrice($specialPrice['special_price'] ?? '')
                ->setSpecialFromDate($specialPrice['date_from'] ?? '')
                ->setSpecialToDate($specialPrice['date_to'] ?? '');
            $repository->save($productNew);

            $productNew = $repository->getById($product->getId(), true, 2, true);
            $productNew->setSpecialPrice(null)
                ->setSpecialFromDate(null)
                ->setSpecialToDate(null);
            $repository->save($productNew);

            $connection = ObjectManager::getInstance()->get(\Magento\Framework\App\ResourceConnection::class)->getConnection();
            $attributeRep = ObjectManager::getInstance()->get(\Magento\Eav\Model\ResourceModel\Entity\Attribute::class);
            $attributeId = $attributeRep->getIdByCode(
                \Magento\Catalog\Model\Product::ENTITY,
                'price'
            );
            $connection->delete(
                'catalog_product_entity_decimal',
                [
                    'attribute_id = ?' => $attributeId,
                    'entity_id = ?' => $product->getId(),
                    'store_id <> ?' => 0
                ]
            );
        } catch (\Exception $e) {
            ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class)->critical("SPS PRICE: " . $e->getMessage());
        }
    }

    /**
     * @param $dataId
     *
     * @return bool
     */
    public function checkProductExistByItemCode($dataId)
    {
        //$query = "SELECT entity_id FROM catalog_product_entity where entity_id in (select entity_id from
        // catalog_product_entity_varchar WHERE attribute_id=167 and VALUE LIKE '$dataId') limit 1";
        //echo $dataId;
        $productcollection = $this->productCollectionFactory->create();
        $productcollection->addAttributeToSelect('*');
        $productcollection->addAttributeToFilter('interprise_item_code', $dataId);
        $productcollection->setPageSize(1)->setCurPage(1);

        if ($productcollection->getSize() > 0) {
            $result = $productcollection->getData();

            return $result[0]['entity_id'];
        } else {
            return false;
        }
    }

    public function create_new_attribute_name($string)
    {
        $number = '';
        if (filter_var($string, FILTER_SANITIZE_NUMBER_INT)) {
            $number = "_" . filter_var($string, FILTER_SANITIZE_NUMBER_INT);
        }
        //$strings = preg_replace("/[^A-Za-z?! ]/", "", $string);
        $string = preg_replace('#[^0-9a-z]+#i', '_', strtolower($string));
        //$final_string = "matrix_" . $strings . $number;
        $final_string = $string . $number;

        return strtolower($final_string);
    }

    public function getSpecialPriceForFrontendNew(
        $itemcode,
        $customercode = '',
        $qty = 1,
        $currency = 'GBP',
        $unitofmeasure = "EACH"
    ) {
        $magento_product_id = $this->checkProductExistByItemCode($itemcode);
        $set_product = $this->productfactory->create()->load($magento_product_id);
        $final_price_magento = $set_product->getFinalPrice();

        $customerSession = $this->session;
        $priceList = json_decode((string)$customerSession->getPriceList(), true);
        //print_r($priceList);
        $calc_min = $final_price_magento;

        if (isset($priceList)) {
            if (count($priceList) > 0) {
                if (array_key_exists($magento_product_id, $priceList)) {
                    if (array_key_exists($currency, $priceList[$magento_product_id])) {
                        foreach ($priceList[$magento_product_id][$currency] as $data) {
                            if ($qty >= $data['m'] && $qty <= $data['x']) {
                                $calc_min = $data['p'];
                                break;
                            }
                        }
                    }
                }
            }
        }

        return $calc_min;
    }

    public function checkCategory($product)
    {
        $wholesaleCategories = $this->getConfig('setup/general/wholesaleCategory');
        if ($wholesaleCategories != '') {
            $wholesaleCategoryIds = explode(",", $wholesaleCategories);
            //print_r($wholesaleCategoryIds);
            $productCategoryIds = $product->getCategoryIds();
            //print_r($productCategories); die;
            if (!empty($productCategoryIds)) {
                //$productCategoryIds = explode(",", $productCategories);
                $result = !empty(array_intersect($productCategoryIds, $wholesaleCategoryIds));

                return $result;
            }
        }

        return false;
    }
}

