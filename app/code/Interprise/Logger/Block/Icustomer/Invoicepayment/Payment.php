<?php
namespace Interprise\Logger\Block\Icustomer\Invoicepayment;

class Payment extends \Magento\Framework\View\Element\Template
{
    public $_session;

    /**
     * Payment constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $session
     * @param \Interprise\Logger\Model\CustompaymentFactory $custompaymentFactory
     * @param \Interprise\Logger\Model\CustompaymentitemFactory $custompaymentitemFactory
     * @param \Magento\Framework\Pricing\Helper\Data $pricingmagento
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $session,
        \Interprise\Logger\Model\CustompaymentFactory $custompaymentFactory,
        \Interprise\Logger\Model\CustompaymentitemFactory $custompaymentitemFactory,
        \Magento\Framework\Pricing\Helper\Data $pricingmagento,
        array $data = []
    ) {
            $this->_custompaymentFactory    = $custompaymentFactory;
            $this->_custompaymentitemFactory    = $custompaymentitemFactory;
            
            $this->pricehelper         = $pricingmagento;
            parent::__construct($context, $data);
            $this->_session                 = $session;
    }
    public function getInvoicepaymentform()
    {
        $this->_session = $this->_session;
        if ($this->_session->isLoggedIn()) {
            $customer_id = $this->_session->getData('customer_id');
            $data = $this->getRequest()->getPostValue();
            $total_amount=0;
            foreach ($data['selectd_value'] as $item => $_item) {
                $total_amount = $total_amount + (float) $_item;
            }
            $model =  $this->_custompaymentFactory->create();
            $model->setCustomerId($data['customer_id']);
            $model->setCreatedAt(date('Y-m-d'));
            $model->setAmount($total_amount);
            $model->setUniqueCode($data['unique_code']);
            $model->setStatus('pending');
            $model->save();
            if ($model->getId()) {
                foreach ($data['selectd_value'] as $item => $_item) {
                    $model1 = $this->_custompaymentitemFactory->create();
                    $model1->setPaymentId($model->getId());
                    $model1->setItmeCode($item);
                    $model1->setAmount($_item);
                    $model1->setCreatedAt(date('Y-m-d'));
                }
            }
            $collection =  $this->_custompaymentFactory->create()->getCollection();
            $collection->addFieldToFilter('entity_id', ['eq' => $model->getId()]);
            $collection->setPageSize(1)->setCurPage(1);
            if ($collection->count()>0) {
                $collection = $collection->getFirstItem();
            }
               $this->setInvoicepaymentform($collection);
            return $collection;
        } else {
             $this->_session->authenticate();
        }
        return 0;
    }
       
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
         $this->pageConfig->getTitle()->set(__('Invoice Payment Form'));

        return $this;
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
}
