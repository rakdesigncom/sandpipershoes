<?php
namespace Interprise\Logger\Block\Icustomer\CRM;

class Addnew extends \Magento\Framework\View\Element\Template
{
    public $objectManager;
    public $resource;
    public $connection;
    public $_session;

    /**
     * Addnew constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $session
     * @param \Interprise\Logger\Model\CaseFactory $caseFactory
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $session,
        \Interprise\Logger\Model\CasesFactory $caseFactory
    ) {
            $this->_case  = $caseFactory;
            $this->_session = $session;
        parent::__construct($context);
    }

    /**
     *
     */
    public function getCollectioncrm()
    {
        $this->_session = $this->_session->create();
        if ($this->_session->isLoggedIn()) {
            $customer_id = $this->_session->getData('customer_id');
            $cases  = $this->_case->create();
            $collection = $cases->getCollection();
            $collection->addFieldToFilter('customer_id', ['eq' => $customer_id]);
            $this->setCollectioncrm($collection);
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
