<?php

namespace Interprise\Logger\Block\Icustomer\Returns;

use \Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\ObjectManagerInterface;

class Index extends \Magento\Framework\View\Element\Template
{
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
        \Interprise\Logger\Helper\Data $helper,
        \Fyb\Trade\Helper\Data $tradeHelper,
        array $data = []
    ) {
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->helper = $helper;
        $this->_pricingmagento = $pricingmagento;
        $this->transactionmaster = $transactionmaster;
        $this->tradeHelper = $tradeHelper;
        parent::__construct($context, $data);
        $this->_session = $session;
    }

    public function getCollectionreturns($filter = 'open')
    {
        return $this->getCollectionreturnsB2C($filter);

        if (!$this->tradeHelper->isTradeStore()) {
            return $this->getCollectionreturnsB2C($filter);
        }
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
                if (!isset($filter) && $filter == '' || $filter == 'open') {
                    $createurl = '';
                } else {
                    $createurl = '/history';
                }
                $api_responsc = $this->helper->getCurlData('salesorder/b2b/rma' . $createurl . '?customerCode=' . $cattrValue);
                $collection = [];
                if ($api_responsc['api_error']) {
                    $attribute_data = $api_responsc['results']['data'];
                    foreach ($attribute_data as $key => $value) {
                        $order_status = $value['attributes']['status'];
                        //$document_type=$value['attributes']['type'];

                        $filter_arr = [];
                        if (!isset($filter) && $filter == '' || $filter == 'open') {
                            if ($order_status == 'Close' || $order_status == 'Completed' || $order_status == 'Partial' || $order_status == 'Void') {
                            } else {
                                //if($document_type=='Back Order' || $document_type=='Sales Order'){
                                $collection['alldata'][] = $attribute_data[$key];
                                //}
                            }
                            $filter = "'Close', 'Completed', 'Partial'";
                        } else {
                            if ($order_status == 'Close' || $order_status == 'Completed' || $order_status == 'Partial' || $order_status == 'Void') {
                                //if($document_type=='Back Order' || $document_type=='Sales Order'){
                                $collection['alldata'][] = $attribute_data[$key];
                                //}
                            }
                            $filter = "'Close', 'Completed', 'Partial'";
                        }
                    }
                } else {
                    $collection['alldata'] = '';
                }
            }

            return $collection;
        }

        $this->_session->authenticate();

        return 0;
    }

    public function getCollectionreturnsB2C($filter = 'open')
    {
        $data = [];

        $customer_id = $this->_session->getData('customer_id');
        $customeratt = $this->_customerRepositoryInterface->getById($customer_id);
        $interpriseCustomerCode = $customeratt->getCustomAttribute('interprise_customer_code');
        if (isset($interpriseCustomerCode)) {
            $cattrValue = $interpriseCustomerCode->getValue();
        } else {
            $cattrValue = '';
        }

        if ($cattrValue == '') {
            $collection = [];
        } else {
            $filter_arr[] = 'Close';
            $filter_arr[] = 'Completed';
            $filter_arr[] = 'Partial';
            $filter_arr[] = 'Void';

            $transaction_masters = $this->transactionmaster->create();
            $collection = $transaction_masters->getCollection();
            $collection->addFieldToFilter('customer_id', ['eq' => $cattrValue]);
            $collection->addFieldToFilter('doc_type', ['in' => ['RMA']]);

            if ($filter == 'open') {
                $collection->addFieldToFilter('status', ['nin' => $filter_arr]);
            } else {
                $collection->addFieldToFilter('status', ['in' => $filter_arr]);

            }

            $this->setCollectionquote($collection);
        }

        foreach ($collection as $item) {
            $data[] = [
                'document_code' => $item->getData('document_code'),
                'pocode' => $item->getData('pocode'),
                'updated_at' => $item->getData('updated_at'),
                'shiptoname' => $item->getData('shiptoname'),
                'total' => $item->getData('total'),
                'status' => $item->getData('status'),
            ];
        }

        return $data;
    }

    /**
     * @param $price
     *
     * @return mixed
     */
    public function formatPrice($price)
    {
        $priceHelper = $this->_pricingmagento;
        $formattedPrice = $priceHelper->currency($price, true, false);

        return $formattedPrice;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__('Returns'));

        return $this;
    }
}
