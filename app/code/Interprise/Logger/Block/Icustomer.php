<?php

namespace Interprise\Logger\Block;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use \Magento\Customer\Api\CustomerRepositoryInterface;

class Icustomer extends \Magento\Framework\View\Element\Template
{
    public $objectManager;

    public $resource;

    public $connection;

    public $_session;

    public $_pricingmagento;

    public $_customerRepositoryInterface;

    public $tradeHelper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $session,
        \Magento\Framework\Pricing\Helper\Data $pricingmagento,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Interprise\Logger\Model\TransactionmasterFactory $transactionmaster,
        \Magento\Framework\App\ResourceConnection $resourceCon,
        \Fyb\Trade\Helper\Data $tradeHelper,
        \Interprise\Logger\Helper\Data $helper,
        array $data = []

    ) {
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->resource = $resourceCon;
        $this->helper = $helper;
        $this->connection = $this->resource->getConnection();
        $this->transactionmaster = $transactionmaster;
        $this->tradeHelper = $tradeHelper;
        $this->_pricingmagento = $pricingmagento;
        parent::__construct($context, $data);
        $this->_session = $session;
    }

    public function getCollectionorder($filter = 'open')
    {
        $collection = [];
        if ($this->_session->isLoggedIn()) {
            $customer_id = $this->_session->getData('customer_id');
            $customeratt = $this->_customerRepositoryInterface->getById($customer_id);

            $interpriseCustomerCode = $customeratt->getCustomAttribute('interprise_customer_code');

            if (isset($interpriseCustomerCode)) {
                $cattrValue = $interpriseCustomerCode->getValue();
            } else {
                $cattrValue = '';
            }

            if ($cattrValue == '') {
                $collection['alldata'] = '';
            } else {
                if (!$filter || $filter == 'open') {
                    $createurl = '';
                } else {
                    $createurl = '/history';
                }

//                if ($this->tradeHelper->isTradeStore()) {
                    $api_responsc = $this->helper->getCurlData('salesorder/b2b/order' . $createurl . '?customerCode=' . $cattrValue);
//                } else {
//                    $api_responsc = $this->helper->getCurlData('customer/salesorder?customerCode='.$cattrValue);
//                }

//                print_r($api_responsc); exit;
//                print_r("<pre>");
                $collection = [];
                if ($api_responsc['api_error']) {
                    $attribute_data = $api_responsc['results']['data'];

                    foreach ($attribute_data as $key => $value) {
                        $order_status = $value['attributes']['status'];

//                        print_r([$value]);
//                        print_r([$order_status, $filter]);
                        $filter_arr = [];
                        if (!$filter || $filter == 'open') {
                            if ($order_status == 'Close' || $order_status == 'Completed' || $order_status == 'Partial' || $order_status == 'Void') {
                            } else {
//                                print_r([$attribute_data[$key]]);
                                //if($document_type=='Back Order' || $document_type=='Sales Order'){
                                $collection['alldata'][] = $attribute_data[$key];
                                //}
                            }
//                            $filter = "'Close', 'Completed', 'Partial'";
                        } else {
                            if ($order_status == 'Close' || $order_status == 'Completed' || $order_status == 'Partial' || $order_status == 'Void') {
                                //if($document_type=='Back Order' || $document_type=='Sales Order'){
                                $collection['alldata'][] = $attribute_data[$key];
                                //}
                            }
//                            $filter = "'Close', 'Completed', 'Partial'";
                        }
                    }
                    if (empty($collection)) {
                        $collection['alldata'] = '';
                    }
                } else {
                    $collection['alldata'] = '';
                }
                //$docType = "'Back Order', 'Sales Order'";

            }

//            exit;
            return $collection;
        }

        $this->_session->authenticate();

        return 0;
    }

    public function formatPrice($price)
    {
        $priceHelper = $this->_pricingmagento;
        $formattedPrice = $priceHelper->currency($price, true, false);

        return $formattedPrice;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__('Order'));

        return $this;
    }
}
