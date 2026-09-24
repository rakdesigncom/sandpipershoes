<?php
namespace Interprise\Logger\Model\Catalog\Pricing\Price;

use Interprise\Logger\Helper\Data;
 
class SpecialPrice extends \Magento\Catalog\Pricing\Price\SpecialPrice
{
    public function __construct(
        \Interprise\Logger\Helper\Data $datahelper,
        \Magento\Customer\Model\Session $session
    ) {

        $this->_datahelper       = $datahelper;
        $this->_session          = $session;
    }
    public function getSpecialPrice()
    {
        $v=1;
//        $specialPrice = $this->product->getSpecialPrice();
//        $interprise_item_code = $this->product->getData('interprise_item_code');
//        $interprise_unitmeasurecode = $this->product->getData('is_unitmeasurecode');
//        $helper_class = $this->_datahelper->create();
//        $customerSession = $this->_session->create();
//        $special_pr = $helper_class->getSpecialPriceForFrontend(
//            $interprise_item_code,
//            $customerSession->getISCode(),
//            1,
//            "GBP",
//            $interprise_unitmeasurecode
//        );
//        if ($specialPrice !== null && $specialPrice !== false && !$this->isPercentageDiscount()) {
//            if (isset($special_pr) && $special_pr>0) {
//                $specialPrice =  min($special_pr, $specialPrice);
//            }
//            $specialPrice = $this->priceCurrency->convertAndRound($specialPrice);
//            return $specialPrice;
//        }
//        if (isset($special_pr) && $special_pr>0) {
//            return $this->priceCurrency->convertAndRound($special_pr);
//        }
    }
}
