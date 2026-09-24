<?php

namespace Fyb\OutOfStockNotification\Helper;

class Data extends \Magento\Framework\Url\Helper\Data
{

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $_storeManager;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
    ) {
        parent::__construct($context);

        $this->_storeManager = $storeManager;
    }

    /**
     * @param $id
     *
     * @return string
     */
    public function getStockAlertUrl($id)
    {
        return $this->_getUrl(
            'productalert/add/stock',
            [
                'product_id' => $id,
                \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl(),
            ]
        );
    }


    /**
     * Check whether stock alert is allowed
     *
     * @return bool
     */
    public function isStockAlertAllowed()
    {
        return $this->scopeConfig->isSetFlag(
            \Magento\ProductAlert\Model\Observer::XML_PATH_STOCK_ALLOW,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
