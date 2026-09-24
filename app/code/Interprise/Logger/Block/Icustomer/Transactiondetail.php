<?php
namespace Interprise\Logger\Block\Icustomer;

class Transactiondetail extends \Magento\Framework\View\Element\Template
{
    public $_session;
    public $request;
    public $loggerhelper;

    /**
     * Detail constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Interprise\Logger\Helper\Data $helper
     * @param \Magento\Catalog\Model\Product $product
     * @param \Interprise\Logger\Model\TransactiondetailFactory $transactiondetail
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $session,
        \Magento\Framework\App\Request\Http $request,
        \Interprise\Logger\Helper\Data $helper,
        \Magento\Catalog\Model\Product $product,
        \Interprise\Logger\Model\TransactiondetailFactory $transactiondetail,
        \Magento\Framework\Pricing\Helper\Data $mhelper
    ) {
            $this->request = $request;
            $this->loggerhelper = $helper;
            $this->product = $product;
            $this->customerSession = $session;
            $this->_transactiondtl = $transactiondetail;
            $this->_mhelper = $mhelper;
            parent::__construct($context);
    }

    /**
     *
     */
    public function getTransactiondetail()
    {
        $collection= array();
        $paramters = $this->request->getParams();
         $id = base64_decode(urldecode($paramters['id']));
         $this->customerSession->getCustomer()->getId();


        if ($this->customerSession->getCustomer()->getId()) {
            //echo '<br/>customer'.$customer_id = $this->_session->getData('customer_id');
            $api_responsc = $this->loggerhelper->getCurlData("invoice/b2b/detail?code=".$id);
            //$api_responsc = $this->loggerhelper->getCurlData("invoice/".$id);
            if($api_responsc['api_error']){
                $attribute_data= $api_responsc['results']['data'];
                $collection['allDetail']=$attribute_data;
              }else{
      					$collection['allDetail']='';
              }
            return $collection;
        } else {
            $this->_session->authenticate();
        }
        return 0;
    }
    public function getHelperMagento()
    {
        return $this->_mhelper;
    }

    /**
     * @param $itemcode
     * @return bool
     */
    public function getMagentoSkuByItemcode($itemcode)
    {
        $product_id  = $this->loggerhelper->checkProductExistByItemCode($itemcode);
        if (isset($product_id)) {
            $product_load  =  $this->product->load($product_id);
            $name = $product_load->getSku();
            return $name;
        } else {
            return false;
        }
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
         $this->pageConfig->getTitle()->set(__('Transaction Detail'));
        return $this;
    }
}
