<?php

namespace Interprise\Logger\Observer\Price;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class Updatecart
 *
 * @package VendorName\Changeprice\Observer
 */
class Updatecart implements ObserverInterface
{
    /**
     * @var CheckoutSession
     */
    protected $_checkoutSession;

    protected $_product;

    protected $_helper;

    protected $_customerSession;

    protected $_storeManager;

    /**
     * Updatecart constructor.
     *
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        \Interprise\Logger\Helper\Data $helper,
        \Magento\Catalog\Model\Product $product,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
        $this->_product = $product;
        $this->_helper = $helper;
        $this->_storeManager = $storeManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        return;
        //echo "<br/>Inside Updatecart";
        $data = $observer->getEvent()->getData('info');

        $cart = $observer->getEvent()->getData('cart');
        $price = '';

        $convert_data = (array)$data;
        //$objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        foreach ($convert_data as $itemsdata => $datainfo) {
            foreach ($datainfo as $itemId => $itemInfo) {
                $item = $this->_checkoutSession->getQuote()->getItemById($itemId);

                if (!$item) {
                    continue;
                }

                $qty = $item->getQty();

                $set_product = $this->_product->load($item->getProductId());
                $itemcode = $set_product->getInterpriseItemCode();
                $unitmeasurecode = $set_product->getIsUnitmeasurecode();
                $final_price = $set_product->getFinalPrice();
                $currencyCode = $this->getStoreCurrency();
                $helper_class = $this->_helper;
                $special_pr = $helper_class->getSpecialPriceForFrontendNew($itemcode, '', $qty, $currencyCode, $unitmeasurecode);

                // add your logic for custom price
                if ($special_pr <= 0) {
                    $special_pr = $final_price;
                }

                $item->setOriginalCustomPrice($special_pr);
                $item->setCustomPrice($special_pr);
            }
        }
    }

    public function getStoreCurrency()
    {
        return $currencycode = $this->_storeManager->getStore()->getCurrentCurrencyCode();
        //return $this->localecurrency->getCurrency($currencycode)->getSymbol();
    }
}
