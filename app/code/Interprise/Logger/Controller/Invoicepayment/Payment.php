<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Index
 *
 * @author geuser1
 */

namespace Interprise\Logger\Controller\Invoicepayment;

use Interprise\Logger\Model\Custompayment;
use Magento\Framework\Controller\ResultFactory;

class Payment extends \Magento\Framework\App\Action\Action
{

    const SAGEPAY_URL = "https://test.sagepay.com/gateway/service/vspserver-register.vsp";

    const SAGEPAY_MODE = "test";

    const SAGEPAY_PAYMENT_TYPE = "PAYMENT";

    const SAGEPAY_VENDOR = "magicballoons";

    const PAYPAL_EMAIL = "deepak02.webnexus-facilitator@gmail.com";

    const PAYPAL_RETURN_URL = "http://127.0.0.1/magento224/icustomer/invoicepayment/returns/";

    const PAYPAL_CANCLE_URL = "http://127.0.0.1/magento224/icustomer/invoicepayment/cancel/";

    const PAYPAL_NOTIFY_URL = "http://127.0.0.1/magento224/icustomer/invoicepayment/notify/";

    const NOTIFICATIONURL = "http://127.0.0.1/magento224/icustomer/invoicepayment/sagenotify/";

    const SUCCESSURL = "http://127.0.0.1/magento224/icustomer/invoicepayment/sagepay/";

    const REDIRECTURL = "http://127.0.0.1/magento224/icustomer/invoicepayment/sagepay/";

    const FAILUREURL = "http://127.0.0.1/magento224/icustomer/invoicepayment/sagepay/";

    public $_pageFactory;

    public $connection;

    public $eoln = "\r\n";

    protected $_curl;

    protected $_custompaymentfactory;

    protected $resultJsonFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Interprise\Logger\Model\CustompaymentFactory $custompaymentFactory,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->_pageFactory = $pageFactory;
        $this->_curl = $curl;
        $this->_custompaymentfactory = $custompaymentFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $total_amount = 0;
        if (isset($data['payform']) && $data['payform'] == 'submitform') {
            $paymetn_option = $data['payment_option'];
            if ($paymetn_option == 'paypal') {
                $this->paypalRequest($data);
            } else if ($paymetn_option == 'sagepay') {
                $maindata = $this->generateData($data);
                $url = self::SAGEPAY_URL;
                $responce = $this->requestPost($maindata, $url);
                $json_encode = json_encode(['status' => true, 'responce' => $responce]);
                $jsonResult = $this->resultJsonFactory->create();
                $jsonResult->setData($json_encode);

                return $jsonResult;
            }
        }

        if (isset($data['selectd_value']) && is_array($data['selectd_value'])) {
            foreach ($data['selectd_value'] as $item => $_item) {
                $total_amount = $total_amount + (float)$_item;
            }
        } else {
            $uc = '';
            if (isset($data['unique_code'])) {
                $uc = $data['unique_code'];
            }
            $this->_redirect('icustomer/invoicepayment/index/', ['uc' => $uc]);
        }
        if ($total_amount > 0) {
            $resultPage = $this->_pageFactory->create();
            $resultPage->getLayout()->initMessages();

            return $resultPage;
        } else {
            $this->_redirect('icustomer/invoicepayment/index/', ['uc' => $data['unique_code']]);
        }
    }

    public function paypalRequest($data)
    {
        $payment = $this->getPaymentInfo($data['payid']);
        $paypal = '';
        $paypal .= '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>';
        $paypal .= '<form id="submit_pay" action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">';
        $paypal .= '<input type="hidden" name="cmd" value="_xclick">';
        $paypal .= '<input type="hidden" name="business" value="' . self::PAYPAL_EMAIL . '">';
        $paypal .= '<input type="hidden" name="amount" value="' . $payment['amount'] . '">';
        $paypal .= '<input type="hidden" name="custom" value="' . $payment['entity_id'] . '">';
        $paypal .= '<input type="hidden" name="currency_code" value="GBP">';
        $paypal .= '<input type="hidden" name="first_name" value="' . $data['BillingFirstnames'] . '">';
        $paypal .= '<input type="hidden" name="last_name" value="' . $data['BillingSurname'] . '">';
        $paypal .= '<input type="hidden" name="address1" value="' . $data['BillingAddress1'] . '">';
        $paypal .= '<input type="hidden" name="address2" value="' . $data['BillingAddress2'] . '">';
        $paypal .= '<input type="hidden" name="city" value="' . $data['BillingCity'] . '">';
        $paypal .= '<input type="hidden" name="country" value="' . $data['BillingCountry'] . '">';
        $paypal .= '<input type="hidden" name="zip" value="' . $data['BillingPostCode'] . '">';
        $paypal .= '<input type="hidden" name="email" value="' . $data['CustomerEMail'] . '">';
        $paypal .= '<input type="hidden" name="cancel_return" value="' . self::PAYPAL_CANCLE_URL . '">';
        $paypal .= '<input type="hidden" name="return" value="' . self::PAYPAL_RETURN_URL . '">';
        $paypal .= '<input type="hidden" name="notify_url" value="' . self::PAYPAL_NOTIFY_URL . '">';
        $paypal .= '<input type="hidden" name="rm" value="2" />';
        $paypal .= '</form>';
        $paypal .= '
		<script>
			jQuery(document).ready(function(){
			    jQuery("#submit_pay").submit();
			});
			</script>
		';
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $resultJson->setContents($paypal);

        return $resultJson;
    }

    public function getPaymentInfo($id)
    {
        $model = $this->_custompaymentfactory->create()->load($id);
        $result = $model->getData();

        return $result;
    }

    public function generateData($data)
    {
        $pay_data = [];
        $payment = $this->getPaymentInfo($data['payid']);
        $pay_data['VPSProtocol'] = '3.00';
        $pay_data['ReferrerID'] = $this->getReferrerID();
        $pay_data['TxType'] = self::SAGEPAY_PAYMENT_TYPE;
        $pay_data['InternalTxtype'] = self::SAGEPAY_PAYMENT_TYPE;
        $pay_data['Vendor'] = self::SAGEPAY_VENDOR;
        $pay_data['User'] = $data['CustomerEMail'];

        $pay_data['Description'] = 'Manual Payment';
        $pay_data['VendorTxCode'] = $this->getVendorCode();
        $pay_data['NotificationURL'] = trim(self::NOTIFICATIONURL . "?PAY_ID=" . $data['payid']);
        $pay_data['SuccessURL'] = self::SUCCESSURL;
        $pay_data['RedirectURL'] = self::REDIRECTURL;
        $pay_data['FailureURL'] = self::FAILUREURL;
        $pay_data['Profile'] = 'NORMAL';
        $pay_data['AllowGiftAid'] = '0';
        $pay_data['Amount'] = $payment['amount'];
        $pay_data['Currency'] = 'GBP';
        $pay_data['BillingAddress'] = $data['BillingAddress1'] . " " . $data['BillingAddress2'];
        $pay_data['BillingSurname'] = $data['BillingSurname'];
        $pay_data['BillingFirstnames'] = $data['BillingFirstnames'];
        $pay_data['BillingPostCode'] = $data['BillingPostCode'];
        $pay_data['BillingAddress1'] = $data['BillingAddress1'];
        $pay_data['BillingAddress2'] = $data['BillingAddress2'];
        $pay_data['BillingCity'] = $data['BillingCity'];
        $pay_data['BillingCountry'] = $data['BillingCountry'];
        $pay_data['CustomerName'] = $data['BillingFirstnames'] . " " . $data['BillingSurname'];
        $pay_data['ContactNumber'] = $data['ContactNumber'];
        $pay_data['CustomerEMail'] = $data['CustomerEMail'];
        $pay_data['DeliveryAddress'] = $data['BillingAddress1'] . " " . $data['BillingAddress2'];
        $pay_data['DeliverySurname'] = $data['BillingSurname'];
        $pay_data['DeliveryFirstnames'] = $data['BillingFirstnames'];
        $pay_data['DeliveryPostCode'] = $data['BillingPostCode'];
        $pay_data['DeliveryAddress1'] = $data['BillingAddress1'];
        $pay_data['DeliveryAddress2'] = $data['BillingAddress2'];
        $pay_data['DeliveryCity'] = $data['BillingCity'];
        $pay_data['DeliveryCountry'] = $data['BillingCountry'];
        $pay_data['ApplyAVSCV2'] = 0;
        $pay_data['CreateToken'] = 1;
        $pay_data['Website'] = 'Main Website';

        return $pay_data;
    }

    public function getReferrerID()
    {
        return $this->createRandom(8) . "-" . $this->createRandom(4) . "-" . $this->createRandom(4) . "-"
            . $this->createRandom(4) . "-" . $this->createRandom(12);
    }

    public function createRandom($lenght = 5, $charType = 'both')
    {
        if ($charType == 'int') {
            $characters = '0123456789';
        } else {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        }

        $randstring = '';
        for ($i = 0; $i < $lenght; $i++) {
            $randstring .= $characters[rand(0, strlen($characters))];
        }

        return $randstring;
    }

    public function requestPost($data, $url)
    {
        $aux = $data;
        if (isset($aux['CardNumber'])) {
            $aux['CardNumber'] = substr_replace($aux['CardNumber'], "XXXXXXXXXXXXX", 0, strlen($aux['CardNumber']) - 3);
        }
        if (isset($aux['CV2'])) {
            $aux['CV2'] = "XXX";
        }
        $rd = '';
        foreach ($data as $_key => $_val) {
            if ($_key == 'billing_address1') {
                $_key = 'BillingAddress1';
            }
            $rd .= $_key . '=' . urlencode(iconv('UTF-8', 'ISO-8859-1', $_val)) . '&';
        }
        $userAgent = "Ebizmarts/SagePaySuite CE(v3.0.9)";
        $_timeout = 999999;
        $timeout = ($_timeout > 0 ? $_timeout : 90);
        $verifyPeerConfig = 0;
        $verifyPeer = 1 === $verifyPeerConfig ? true : false;
        $this->_curl->setOption(CURLOPT_USERAGENT, $userAgent);
        $this->_curl->setOption(CURLOPT_URL, $url);
        $this->_curl->setOption(CURLOPT_HEADER, 0);
        $this->_curl->setOption(CURLOPT_POST, 1);
        $this->_curl->setOption(CURLOPT_POSTFIELDS, $rd);
        $this->_curl->setOption(CURLOPT_RETURNTRANSFER, 1);
        $this->_curl->setOption(CURLOPT_TIMEOUT, $timeout);
        $this->_curl->setOption(CURLOPT_SSL_VERIFYPEER, $verifyPeer);
        $this->_curl->setOption(CURLOPT_SSL_VERIFYHOST, 2);
        $this->_curl->post($url, $rd);
        $response = $this->_curl->getBody();
        $counts = count($response);
        for ($i = 0; $i < $counts; $i++) {
            $splitAt = strpos($response[$i], "=");
            // Create an associative (hash) array with key/value pairs ('trim' strips excess whitespace)
            $arVal = (string)trim(substr($response[$i], ($splitAt + 1)));
            if (!empty($arVal)) {
                $output[trim(substr($response[$i], 0, $splitAt))] = $arVal;
            }
        }

        return $output;
    }
}
