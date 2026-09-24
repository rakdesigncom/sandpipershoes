<?php

namespace Fyb\Trade\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;

class CustomerPricing implements SectionSourceInterface
{
    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Fyb\Trade\Helper\Data
     */
    protected $helper;
    public function __construct(
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Fyb\Trade\Helper\Data $helper
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->_scopeConfig = $scopeConfig;
        $this->helper = $helper;
    }


    public function getSectionData()
    {
        if (!$this->currentCustomer->getCustomerId()) {
            return [];
        }

        $customer = $this->currentCustomer->getCustomer();

        return $this->helper->getCustomerData($customer);
    }
}
