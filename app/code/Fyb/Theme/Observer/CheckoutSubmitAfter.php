<?php

namespace Fyb\Theme\Observer;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Api\SimpleDataObjectConverter;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

class CheckoutSubmitAfter implements ObserverInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepositoryInterface;

    /**
     * @var \Magento\Customer\Api\AccountManagementInterface
     */
    protected $accountManagementInterface;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Api\SimpleDataObjectConverter
     */
    protected $simpleDataObjectConverter;

    /**
     * @param \Magento\Framework\Api\SimpleDataObjectConverter $simpleDataObjectConverter
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     * @param \Magento\Customer\Api\AccountManagementInterface $accountManagementInterface
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        SimpleDataObjectConverter $simpleDataObjectConverter,
        Session $customerSession,
        CustomerRepositoryInterface $customerRepositoryInterface,
        AccountManagementInterface $accountManagementInterface,
        StoreManagerInterface $storeManager,
    ) {
        $this->customerSession = $customerSession;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->accountManagementInterface = $accountManagementInterface;
        $this->storeManager = $storeManager;
        $this->simpleDataObjectConverter = $simpleDataObjectConverter;
    }

    /**
     * Handle order tying to guest customer
     *
     * @inheritdoc
     */
    public function execute(
        Observer $observer
    ) {
        $quote = $observer->getEvent()->getQuote();
        $order = $observer->getEvent()->getOrder();
        if (is_object($order) && $order->getStoreId() == "0") {
            return;
        }

        $email = $quote->getCustomerEmail();
        $isLoggedIn = $this->customerSession->getId();
        $isEmailAvailable  = (int)$this->accountManagementInterface
            ->isEmailAvailable($quote->getCustomerEmail());

        if (!$isLoggedIn  && !$isEmailAvailable) {
            try {
                $customer = $this->customerRepositoryInterface->get($email, $this->storeManager->getWebsite()->getId());

                if (!empty($order) && is_object($order) && is_object($customer)) {
                    $this->addUserToOrder($quote, $order, $customer);
                    if ($order->getCustomerId() && $order->getCustomerIsGuest()) {
                        $order->setCustomerIsGuest(0);
                    }

                    $order->save();
                    $quote->save();
                }
            } catch (NoSuchEntityException $e) {
            }
        }
    }

    /**
     * Add user to order
     *
     * @param $quote
     * @param $order
     * @param $customer
     */
    private function addUserToOrder(
        $quote,
        $order,
        $customer
    ) {
        $orderCustomerAttr = array_keys($order->getData());
        foreach ($orderCustomerAttr as $k) {
            if (strpos($k, 'customer_') !== false) {
                $k = str_replace('customer_', '', $k);

                $convertedKey = $this->simpleDataObjectConverter::snakeCaseToUpperCamelCase($k);
                $setMethodName = 'setCustomer' . $convertedKey;
                $getMethodName = 'get' . $convertedKey;

                if (method_exists($customer, $getMethodName)) {
                    // phpcs:ignore Magento2.Functions.DiscouragedFunction
                    $v = call_user_func([
                        $customer,
                        $getMethodName
                    ]);

                    if ($v) {
                        // phpcs:ignore Magento2.Functions.DiscouragedFunction
                        call_user_func([
                            $order,
                            $setMethodName
                        ], $v);
                        // phpcs:ignore Magento2.Functions.DiscouragedFunction
                        call_user_func([
                            $quote,
                            $setMethodName
                        ], $v);
                    }
                }
            }
        }
    }
}
