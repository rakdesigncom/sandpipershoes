<?php


namespace Interprise\Logger\Observer\Customer;

class Addressupdate implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public $_registry;
    public $_session;
    public $_addressfactory;
    public $_clogfactory;
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Session\SessionManagerInterface $session,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Interprise\Logger\Model\ChangelogFactory $changelogFactory
    ) {
        $this->_registry  = $registry;
        $this->_session  = $session;
        $this->_addressfactory  = $addressFactory;
        $this->_clogfactory  = $changelogFactory;
    }
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $address = $observer->getEvent()->getCustomerAddress();
        $addressid = $address->getId();
        $addresss = $this->_addressfactory->create();
        $address = $address->load($addressid);
        $api_address = $address->getInterpriseApicreatedaddress();
        if ($address->getInterpriseApicreatedaddress()<>1) {
            $model = $this->_clogfactory->create();
            $model->setData('CreatedAt', NOW());
            $model->setData('ItemType', 'address');
            $model->setData('ItemId', $addressid);
            $model->setData('Action', 'post');
            $model->setData('PushedStatus', 0);
            $model->save();
        }
    }
}
