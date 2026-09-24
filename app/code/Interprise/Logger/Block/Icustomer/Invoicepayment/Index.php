<?php
namespace Interprise\Logger\Block\Icustomer\Invoicepayment;

class Index extends \Magento\Framework\View\Element\Template
{
    public $_session;

    /**
     * Index constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $session
     * @param \Interprise\Logger\Model\StatementaccountFactory $statementaccount
     * @param \Magento\Framework\Pricing\Helper\Data $pricingmagento
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $session,
        \Interprise\Logger\Model\StatementaccountFactory $statementaccount,
        \Magento\Framework\Pricing\Helper\Data $pricingmagento,
        array $data = []
    ) {
            
            $this->_statementaccount    = $statementaccount;
             $this->pricehelper         = $pricingmagento;
            parent::__construct($context, $data);
            $this->_session             = $session;
    }

    /**
     *
     */
    public function getInvoicepayments()
    {
        $this->_session = $this->_session;
        if ($this->_session->isLoggedIn()) {
            $customer_id = $this->_session->getData('customer_id');
            $collection = $this->_statementaccount->create();
            $collection = $collection->getCollection();
            $collection->addFieldToFilter('customer_id', ['eq' => $customer_id]);
            $collection->addFieldToFilter('document_type', ['eq' => 'Invoice']);
            $collection->setOrder('due_date', 'ASC');
            $this->setInvoicepayments($collection);
            return $collection;
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
         $this->pageConfig->getTitle()->set(__('Invoice Payment'));
        return $this;
    }
}
