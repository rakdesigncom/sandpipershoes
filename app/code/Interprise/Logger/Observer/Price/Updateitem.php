<?php

namespace Interprise\Logger\Observer\Price;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class Updatecart
 *
 * @package VendorName\Changeprice\Observer
 */
class Updateitem implements ObserverInterface
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
        //echo "<br/>Inside Updateitem1";

        //$data = $observer->getEvent()->getData('info');

        $item = $observer->getEvent()->getData('quote_item');
        $item = ($item->getParentItem() ? $item->getParentItem() : $item);
        $price = '';
        $qty = $item->getQty();

        //print_r($cart);
        $set_product = $this->_product->load($item->getProductId());
        $final_price = $set_product->getFinalPrice();
        $itemcode = $set_product->getInterpriseItemCode();
        $unitmeasurecode = $set_product->getIsUnitmeasurecode();
        $helper_class = $this->_helper;
        $currencyCode = $this->getStoreCurrency();
        $special_pr = $helper_class->getSpecialPriceForFrontendNew($itemcode, '', $qty, $currencyCode, $unitmeasurecode);

        if ($special_pr <= 0) {
            $special_pr = $final_price;
        }
        $price = $special_pr; //set your price here

        $item->setCustomPrice($price);
        $item->setOriginalCustomPrice($price);
        $item->getProduct()->setIsSuperMode(true);
    }

    public function getStoreCurrency()
    {
        return $currencycode = $this->_storeManager->getStore()->getCurrentCurrencyCode();
        //return $this->localecurrency->getCurrency($currencycode)->getSymbol();
    }
}
