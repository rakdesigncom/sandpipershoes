<?php

namespace Interprise\Logger\Block\Icustomer\Transaction;

use \Magento\Customer\Api\CustomerRepositoryInterface;

//use Magento\Framework\ObjectManagerInterface;

class Index extends \Magento\Framework\View\Element\Template
{
    public $_session;

    public $_pricingmagento;

    public $_customerRepositoryInterface;

    public $_objectManager;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $session,
        \Magento\Framework\Pricing\Helper\Data $pricingmagento,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Interprise\Logger\Model\TransactionmasterFactory $transactionmaster,
        \Interprise\Logger\Helper\Data $helper,
        array $data = []
    ) {
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->helper = $helper;
        $this->_pricingmagento = $pricingmagento;
        $this->transactionmaster = $transactionmaster;
        // $this->_objectManager = $objectmanager;

        parent::__construct($context, $data);
        $this->_session = $session;
    }

    public function getCollectiontransaction($filter = 'open')
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
                $api_responsc = $this->helper->getCurlData('invoice/b2b/transactionlist?customerCode=' . $cattrValue);

                if ($api_responsc['api_error']) {
                    $docType = ['Invoice', 'Credit Memo', 'Opening Invoice'];
                    $attribute_data = $api_responsc['results']['data'];

                    foreach ($attribute_data as $key => $value) {
                        $document_type = $value['attributes']['type'];
                        if (!in_array($document_type, $docType)) {
                            continue;
                        }
                        $balance = round($value['attributes']['outstanding']);

                        if ($filter == '' || $filter == 'open') {
                            if ($balance != 0) {
                                $collection['alldata'][] = $value;
                            }
                        } else {
                            if ($balance == 0) {
                                $collection['alldata'][] = $value;
                            }
                        }
                    }

                    if (empty($collection)) {
                        $collection['alldata'] = '';
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
        $this->pageConfig->getTitle()->set(__('Transaction'));

        return $this;
    }
}
