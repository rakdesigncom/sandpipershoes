<?php
namespace Interprise\Logger\Block\Icustomer\Quote;

use \Magento\Customer\Api\CustomerRepositoryInterface;

class Index extends \Magento\Framework\View\Element\Template
{
    public $_session;
    public $_pricingmagento;
    public $_customerRepositoryInterface;
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
            $this->transactionmaster = $transactionmaster;
            $this->_pricingmagento = $pricingmagento;
            parent::__construct($context, $data);
            $this->_session = $session;
    }
    public function getCollectionquote()
    {
        $collection = array();
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
            } else{

              $filter = $this->getRequest()->getParam('filter');
              if(!isset($filter) && $filter=='' || $filter=='open'){
                $createurl='';
              }else{
                $createurl='/history';
              }
              $api_responsc = $this->helper->getCurlData('salesorder/b2b/quote'.$createurl.'?customerCode='.$cattrValue);
              //$api_responsc = $this->helper->getCurlData('customer/salesorder?customerCode='.$cattrValue);
              //$collection= array();
              if($api_responsc['api_error'])
              {
        				$attribute_data= $api_responsc['results']['data'];
                foreach ($attribute_data as $key => $value) {
                  $order_status=$value['attributes']['status'];
                  //$document_type=$value['attributes']['type'];

                  $filter = $this->getRequest()->getParam('filter');
                    $filter_arr = [];
                    if (!isset($filter) && $filter=='' || $filter=='open') {
                      if( $order_status=='Close' || $order_status=='Completed' ||$order_status=='Partial' || $order_status=='Void'){

                    }else{
                      //if($document_type=='Quote'){
          							$collection['alldata'][]=$attribute_data[$key];
          						//}
                    }
                         $filter = "'Close', 'Completed', 'Partial'";
                    } else {
                      if( $order_status=='Close' || $order_status=='Completed' ||$order_status=='Partial' || $order_status=='Void'){
              						//if($document_type=='Quote'){
              							$collection['alldata'][]=$attribute_data[$key];
              					//	}
                      }
                         $filter = "'Close', 'Completed', 'Partial'";
                    }
                }

        			}else{
        			$collection['alldata']='';
        			}

                return $collection;
            }
        } else {
            //header("https://www.sandpipershoes.com/customer/account/login/referer/aHR0cHM6Ly9kZW1vLnNhbmRwaXBlcnNob2VzLmNvbS9jdXN0b21lci9hY2NvdW50L2xvZ291dFN1Y2Nlc3Mv/");
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
        $priceHelper =$this->_pricingmagento;
        $formattedPrice = $priceHelper->currency($price, true, false);
        return $formattedPrice;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
         $this->pageConfig->getTitle()->set(__('Quote'));
        return $this;
    }
}
