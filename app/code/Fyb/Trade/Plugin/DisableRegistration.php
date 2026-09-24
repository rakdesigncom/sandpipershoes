<?php

namespace Fyb\Trade\Plugin;

use Magento\Customer\Model\Registration;

class DisableRegistration
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Magento\Customer\Model\Registration $subject
     * @param bool $result
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterIsAllowed(
        Registration $subject,
        $result
    ) {
        $storeCode = $this->storeManager->getWebsite()->getCode();
        if ($storeCode === \Fyb\Trade\Helper\Data::TRADE_STORE_CODE) {
            return false;
        }

        return $result;
    }
}
