<?php
namespace Interprise\Logger\Block\Icustomer\Cstatement;

class Index extends \Magento\Framework\View\Element\Template
{

    public $_session;
    public $_pricingmagento;
    public $_customerRepositoryInterface;

    /**
     * Index constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Framework\Pricing\Helper\Data $pricingmagento
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     * @param \Interprise\Logger\Model\StatementaccountFactory $statementaccount
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $session,
        \Magento\Framework\Pricing\Helper\Data $pricingmagento,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Interprise\Logger\Model\StatementaccountFactory $statementaccount,
        \Interprise\Logger\Helper\Data $helper,
        array $data = []
    ) {
            $this->_customerRepositoryInterface = $customerRepositoryInterface;
            $this->helper = $helper;
            $this->_statementaccount = $statementaccount;
            $this->pricehelper = $pricingmagento;
        parent::__construct($context, $data);
        $this->_session = $session;
    }

    /**
     *
     */
    public function getCollectionstatement()
    {
        $this->_session = $this->_session;
        if ($this->_session->isLoggedIn()) {
            $customer_id = $this->_session->getData('customer_id');
            $customeratt = $this->_customerRepositoryInterface->getById($customer_id);

            $interpriseCustomerCode = $customeratt->getCustomAttribute('interprise_customer_code');

            if(isset($interpriseCustomerCode))
                $cattrValue = $interpriseCustomerCode->getValue();
            else
                $cattrValue='';
                if($cattrValue==''){
                    $collection['alldata']='';
                } else
                {
                  $api_responsc = $this->helper->getCurlData('customer/b2b/statement?customerCode='.$cattrValue);
                  if($api_responsc['api_error']){
            				$attribute_data= $api_responsc['results']['data'];
                    $collection['alldata']=$attribute_data;
                    $api_responsc2 = $this->helper->getCurlData('customer/b2b/statementsummary?customerCode='.$cattrValue);
                    if($api_responsc2['api_error']){
                      $collection['restdata']=$api_responsc2['results']['data'];
                    }else{
                      $collection['restdata']='';
                    }
                    // foreach ($attribute_data as $key => $value) {
                    //   $invoice_code=$value['attributes']['invoiceCode'];
                    //   $collection['alldata'][$key]=$attribute_data[$key];
                    //   $invoice_responsc = $this->helper->getCurlData('invoice/b2b/detail?code='.$invoice_code);
                    //   if($invoice_responsc['api_error']){
                    //     $collection['alldata'][$key]['restdata']=$invoice_responsc['results']['data'];
                    //   }
                    // }

            				//$collection['alldata']='';
            			}else{
                  $collection['alldata']='';
                  }
                  return $collection;
                }

        } else {
             $this->_session->authenticate();
        }
        return 0;
    }

    /**
     * @param $price
     * @return mixed
     */
    public function formatPrice($price)
    {
        $priceHelper = $this->pricehelper;
        $formattedPrice = $priceHelper->currency($price, true, false);
        return $formattedPrice;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
         $this->pageConfig->getTitle()->set(__('Statement'));
        return $this;
    }
}
