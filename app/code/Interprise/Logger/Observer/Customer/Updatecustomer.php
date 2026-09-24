<?php
/**
 * *
 * *@author:Shadab
 * *
 * *
 */

namespace Interprise\Logger\Observer\Customer;

class Updatecustomer implements \Magento\Framework\Event\ObserverInterface
{
    protected $_changelog;

    public function __construct(
        \Interprise\Logger\Model\ChangelogFactory $changelog
    ) {
        $this->_changelog = $changelog;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $customer = $observer->getData('customer_data_object');
        $customerId = $customer->getId();
        $customer_array = $customer->__toArray();
        if (isset($customer_array['custom_attributes']['interprise_apicreated']['value'])) {
            $api_created = $customer_array['custom_attributes']['interprise_apicreated']['value'];
        } else {
            $api_created = 0;
        }
        if ($api_created <> 1) {
            $changelog_master = $this->_changelog->create();
            $changelog_master->setCreatedAt(NOW());
            $changelog_master->setItemType('customer');
            $changelog_master->setItemId($customerId);
            $changelog_master->setAction('post');
            $changelog_master->setPushedStatus(0);
            $changelog_master->save();
        }
    }
}
