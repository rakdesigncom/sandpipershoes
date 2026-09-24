<?php

namespace Interprise\Logger\Observer\Price;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Model\Product\Type;

class Afteraddtocart implements ObserverInterface
{

    protected $_product;

    protected $_helper;

    protected $_customerSession;

    protected $_storeManager;

    public function __construct(
        \Interprise\Logger\Helper\Data $helper,
        \Magento\Catalog\Model\Product $product,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager

    ) {
        $this->_customerSession = $customerSession;
        $this->_product = $product;
        $this->_helper = $helper;
        $this->_storeManager = $storeManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        return;
        //echo "<br/>Inside Afteraddtocart";
        //$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $item = $observer->getEvent()->getData('quote_item');
        if ($item->getProduct()->getTypeId() == Type::TYPE_BUNDLE) {
            return;
            foreach ($item->getQuote()->getAllItems() as $bundleitems) {
                /** @var $bundleitems\Magento\Quote\Model\Quote\Item */
                //Skip the bundle product
                if ($bundleitems->getProduct()->getTypeId() == Type::TYPE_BUNDLE) {
                    continue;
                }
                $set_product = $this->_product->load($bundleitems->getProductId());
                $final_price = $set_product->getFinalPrice();
                $itemcode = $set_product->getInterpriseItemCode();
                $unitmeasurecode = $set_product->getIsUnitmeasurecode();

                //  $helper_class = $objectManager->create('Interprise\Logger\Helper\Data');
                // $special_pr = $helper_class->getSpecialPriceForFrontend($itemcode,'',$qty,'GBP',$unitmeasurecode);
                //   $price = min($special_pr,$final_price); //set your price here
                $bundleitems->setCustomPrice($final_price);
                $bundleitems->setOriginalCustomPrice($final_price);
                $bundleitems->getProduct()->setIsSuperMode(true);
            }
        } else {
            //echo "Inside Afteraddtocart Observer";

            $item = ($item->getParentItem() ? $item->getParentItem() : $item);

            $qty = $item->getQty();

//            $set_product = $this->_product->load($item->getProductId());
            $set_product = $item->getProduct();
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
    }

    public function getStoreCurrency()
    {
        return $currencycode = $this->_storeManager->getStore()->getCurrentCurrencyCode();
        //return $this->localecurrency->getCurrency($currencycode)->getSymbol();
    }

}
