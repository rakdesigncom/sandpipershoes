<?php

namespace Fyb\StockistLocator\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
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
     * @return bool
     */
    public function isEnabled()
    {
        return $this->getConfig('stockistlocator/general/active');
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
}
