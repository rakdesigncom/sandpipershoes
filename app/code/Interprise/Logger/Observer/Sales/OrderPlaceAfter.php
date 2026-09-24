<?php

namespace Interprise\Logger\Observer\Sales;

use Interprise\Logger\Helper\Data;
use Magento\Framework\App\ObjectManager;

class OrderPlaceAfter implements \Magento\Framework\Event\ObserverInterface
{
    protected $customerFactory;

    protected $_changelog;

    protected $_helper;

    protected $_logger;

    protected $customerResource;

    public function __construct(
        \Interprise\Logger\Model\ChangelogFactory $changelog,
        \Interprise\Logger\Helper\Data $helper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\ResourceModel\Customer $customerResource
    ) {
        $this->_changelog = $changelog;
        $this->_helper = $helper;
        $this->_logger = $logger;
        $this->customerFactory = $customerFactory;
        $this->customerResource = $customerResource;
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
        $this->_logger->debug('Interprise Logger OrderPlaceAfter');
        $order = $observer->getOrder();
        //print_r($order_ids);
        $order_id = $order->getId();
        $this->_logger->debug('Interprise Logger OrderPlaceAfter OrderId ' . $order_id);
        if ($order_id != null || $order_id != '') {
            $changelog_master = $this->_changelog->create();
            $changelog_master->setData('CreatedAt', $this->_helper->getCurrentTime());
            $changelog_master->setData('ItemType', 'order');
            $changelog_master->setData('ItemId', $order_id);
            $changelog_master->setData('Action', 'POST');
            $changelog_master->setData('PushedStatus', 0);
            $changelog_master->save();
        }
        $customer_id = $order->getCustomerId();
        $this->_logger->debug('Interprise Logger OrderPlaceAfter CustomerId ' . $customer_id);
        if ($customer_id) {
            $vatExemptCustomer = $order->getVatExemptCustomer();
            $vatExemptReason = $order->getVatExemptReason();
            if ($vatExemptCustomer != '' && $vatExemptReason != '') {
                /** @var \Magento\Customer\Model\Customer $update_customer_factory */
                $customerData = $this->customerFactory->create()->load($customer_id);
                $customerDataModel = $customerData->getDataModel();
                $customerDataModel->setCustomAttribute('vatexemptcustomer_c', $vatExemptCustomer);
                $customerDataModel->setCustomAttribute('vatexemptreason_c', $vatExemptReason);
                $customerData->updateData($customerDataModel);

                $this->customerResource->saveAttribute($customerData, 'vatexemptcustomer_c');
                $this->customerResource->saveAttribute($customerData, 'vatexemptreason_c');

                $vatExemptExpiryDate = $customerData->getData('vatexemptexpirydate_c');
                $currentDate = $this->_helper->getCurrentTime();
                if ($vatExemptExpiryDate != '') {
                    if (strtotime($vatExemptExpiryDate) < strtotime($currentDate)) {
                        $afterThreeYears = date('Y-m-d H:i:s', strtotime('+3 years', strtotime($currentDate)));

                        $customerDataModel->setCustomAttribute('vatexemptexpirydate_c', $afterThreeYears);
                        $customerData->updateData($customerDataModel);

                        $this->customerResource->saveAttribute($customerData, 'vatexemptexpirydate_c');
                    }
                } else {
                    $afterThreeYears = date('Y-m-d H:i:s', strtotime('+3 years', strtotime($currentDate)));

                    $customerDataModel = $customerData->getDataModel();
                    $customerDataModel->setCustomAttribute('vatexemptexpirydate_c', $afterThreeYears);
                    $customerData->updateData($customerDataModel);

                    $this->customerResource->saveAttribute($customerData, 'vatexemptexpirydate_c');
                }
            }
        }
    }
}
