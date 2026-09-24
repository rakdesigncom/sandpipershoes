<?php
namespace Interprise\Logger\Block\Icustomer\CRM;

class Index extends \Magento\Framework\View\Element\Template
{
    public $_session;

    /**
     * Index constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $session
     * @param \Interprise\Logger\Model\CaseFactory $caseFactory
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $session,
        \Interprise\Logger\Model\CasesFactory $caseFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Interprise\Logger\Helper\Data $helper,
        array $data = []
    ) {
            $this->_customerRepositoryInterface = $customerRepositoryInterface;
            $this->_case = $caseFactory;
            $this->helper = $helper;
            parent::__construct($context, $data);
            $this->_session = $session;

    }

    /**
     *
     */
    public function getCollectioncrm()
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
            } else
            {
              $api_responsc = $this->helper->getCurlData('crm/b2b/case?customerCode='.$cattrValue);
              if($api_responsc['api_error'])
              {
                $attribute_data= $api_responsc['results']['data'];
                foreach ($attribute_data as $key => $value) {
                $documentCode=$value['attributes']['documentCode'];
                // echo $documentCode;
                // die;
                $api_responsc2 = $this->helper->getCurlData('crm/b2b/case/detail?caseCode='.$documentCode);
        				$attribute_data2= $api_responsc2['results']['data'][0];
                $collection['alldata'][]=$attribute_data2;
              }
              }else{
                $collection['alldata']='';
              }
              //echo "done";
            }
            // echo "<pre>";
            // print_r($collection);
            // die;
            // $cases  = $this->_case->create();
            // $collection = $cases->getCollection();
            // $collection->addFieldToFilter('customer_id', ['eq' => $customer_id]);
            // $this->setCollectioncrm($collection);
             return $collection;
        } else {
             $this->_session->authenticate();
        }
        return 0;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
         $this->pageConfig->getTitle()->set(__('Cases'));
        return $this;
    }
}
