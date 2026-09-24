<?php

namespace Fyb\Trade\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const TRADE_STORE_CODE = 'sps_trade';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);

        $this->storeManager = $storeManager;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getTradeStoreUrl()
    {
        return rtrim($this->getTradeStore()->getBaseUrl(), '/') . '/customer/account/login';
    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getTradeStore()
    {
        return $this->storeManager->getStore(self::TRADE_STORE_CODE);
    }

    /**
     * @return bool
     */
    public function isTradeStore($storeId = null)
    {
        try {
            if (!$storeId) {
                $storeId = $this->getCurrentStore()->getId();
            }

            return $this->getTradeStore()->getId() === $storeId;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCurrentStore()
    {
        return $this->storeManager->getStore();
    }

    /**
     * @param string $path
     * @param string $scopeType
     * @param null|int|string $scopeCode
     *
     * @return mixed
     */
    public function getConfig($path, $scopeType = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return $this->scopeConfig->getValue($path, $scopeType, $scopeCode);
    }

    public function getCustomerData($customer)
    {
        $discount = 0;
        if ($customer->getGroupId() == 3) {
            $discount = $customer->getCustomAttribute('interprise_discount')?->getValue() ?: 0;
        }

        return [
            'discountInitial' => (int)$discount,
            'discount' => (int)$discount / 100,
            'priceType' => $customer->getCustomAttribute('interprise_defaultprice')?->getValue(),
            'groupId' => $customer->getGroupId(),
        ];
    }
}
