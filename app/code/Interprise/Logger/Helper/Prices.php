<?php
namespace Interprise\Logger\Helper;

use \Interprise\Logger\Helper\Data;

class Prices extends Data
{
    protected $pricelistcollection;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Catalog\Model\ProductFactory $product,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categorycollection,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory  $productCollectionFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryobj,
        \Interprise\Logger\Model\PricingcustomerFactory $pricingcustomer,
        \Interprise\Logger\Model\PricelistsFactory $pricelistsFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Customer\Model\Session $session,
        \Interprise\Logger\Model\CountryclassmappingFactory $classmapping,
        \Interprise\Logger\Model\StatementaccountFactory $statementaccountFactory,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Interprise\Logger\Model\CustompaymentFactory $custompaymentFactory,
        \Interprise\Logger\Model\CustompaymentitemFactory $custompaymentitemFactory,
        \Interprise\Logger\Model\PaymentmethodFactory $paymentmethodfact,
        \Interprise\Logger\Model\ResourceModel\Installwizard\CollectionFactory $installwizardFactory,
        \Interprise\Logger\Model\ShippingstoreinterpriseFactory $shippingstoreinterpriseFactory,
        \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory,
        \Interprise\Logger\Model\ResourceModel\Pricelists\CollectionFactory $pricelistscollectionFactory
    ) {
        $this->pricelistcollection = $pricelistscollectionFactory;
        parent::__construct(
            $context,
            $httpContext,
            $product,
            $curl,
            $datetime,
            $categorycollection,
            $productCollectionFactory,
            $categoryobj,
            $pricingcustomer,
            $pricelistsFactory,
            $productFactory,
            $session,
            $classmapping,
            $statementaccountFactory,
            $addressFactory,
            $custompaymentFactory,
            $custompaymentitemFactory,
            $paymentmethodfact,
            $installwizardFactory,
            $shippingstoreinterpriseFactory,
            $curlFactory
        );
    }
    public function pricesSingle($data)
    {
         ini_set("display_errors","1");
        echo '<br/>New DataId '.$dataId = $data['DataId'];
        $exist_product_in_magento =$this->checkProductExistByItemCode($dataId);
        $update_data['ActivityTime'] = $this->getCurrentTime();
        $update_data['Request'] = "Check Product in Magento";
        $update_data['Response'] = "Product not found in Magento";
        if (!$exist_product_in_magento) {
            $update_data['Status'] = 'Fail';
            $update_data['Remarks'] = 'Product not found in Magento to update PriceLevel';
            return $update_data;
        }
        $product = $this->_product->create()->setStoreId(0)->load($exist_product_in_magento);
        $sku = $product->getSku();
        $prices =$this->getAllPrices($sku, $dataId);
        $product->setPrice($prices['price']);
        $product->setData('is_retailprice', $prices['retail_price']);
        $product->setData('is_wholesaleprice', $prices['wholesale_price']);
        //$product->setSpecialPrice($prices['special_price']);
//        if (isset($prices['dateFrom'])) {
//            $product->setSpecialFromDate($prices['dateFrom']);
//        }
//        if (isset($prices['dateTo'])) {
//            $product->setSpecialToDate($prices['dateTo']);
//        }
           // special price to (MM-DD-YYYY)
        try {
            $product->save();
            $this->updateWhosalePrice($product);

            $update_data['Request'] = "Price update";
            $update_data['Response'] = 'Price updated';
            $update_data['Status'] = 'Success';
            $update_data['Remarks'] = 'Success';
        } catch (Exception $ex) {
            $update_data['Request'] = "Price update";
            $update_data['Response'] = 'Price update failed';
            $update_data['Status'] = 'Fail';
            $update_data['Remarks'] = $ex->message();
        }
        return $update_data;
    }
    public function getAllPrices($item_name, $itemcode, $currency = 'GBP')
    {
        /***It updtes the interprise_price_lists table for this item code
         * get the wholesale price
         * get the retial price
         * get the special price
         * and then returns array of wholesale price as price,base_price as msrp and special price
         ***/

        $array = [];

        /* It updates the interprise_price_lists table for this item code */
        $retail_prices = $this->getRetailPrice($item_name, $currency); //get the retail price and suggested retail price
        $wholesale_prices = $this->getWholesalePrice($item_name, $currency);//get the wholesale price
        //$special_prices  = $this->getSpecialPriceItem($itemcode, $currency);
        $regular_price_field = $this->getConfig('setup/general/regular_price');
        if ($regular_price_field=='Retail') {
            $array['price'] = $retail_prices['base_price'];
        } else {
            $array['price'] = $wholesale_prices['wholesale_price'];
        }
        $array['retail_price'] = $retail_prices['base_price'];
        $array['wholesale_price'] = $wholesale_prices['wholesale_price'];
        $array['msrp'] = $retail_prices['suggested_price'];
        // if (isset($special_prices['special_price'])) {
        //     $array['special_price'] = $special_prices['special_price'];
        // } else {
        //     $array['special_price'] = '';
        // }
        // if (isset($special_prices['dateFrom']) && $special_prices['dateFrom']!=''
        //     &&
        //     isset($special_prices['dateTo']) && $special_prices['dateTo']!=''
        // ) {
        //     $array['dateFrom'] = $special_prices['dateFrom'];
        //     $array['dateTo'] = $special_prices['dateTo'];
        // } else {
        //     $array['dateFrom'] = '';
        //     $array['dateTo'] = '';
        // }
         $this->interprisePriceLists($itemcode, $currency);

        return $array;
    }
    public function interprisePriceLists($itme_code, $_currency_code = 'GBP')
    {
        if ($itme_code=='') {
            return false;
        }
        $collection_prlist =$this->pricelistcollection->create();
        $collection_prlist->addFieldToFilter('itemcode', ['eq' => $itme_code]);
        $collection_prlist->addFieldToFilter('currency', ['eq' => $_currency_code]);
        foreach ($collection_prlist as $colsobj) {
            $colsobj->delete();
        }
        if ($_currency_code=='EUR') {
            $_currency_code='EURO';
        }

        $price_lists='pricing/pricelist/detail?itemCode='.$itme_code."&Currency=".$_currency_code;
        $price_lists_api_result=$this->getCurlData($price_lists);
        if ($price_lists_api_result['api_error']) {
            $result=$price_lists_api_result['results']['data'];
            if (count($result)>0) {
                foreach ($result as $key => $value) {
                    $arribute_price=$value['attributes'];
                    $itemcode=$arribute_price['itemCode'];
                    $pricelist=$arribute_price['pricingLevel'];
                    $currency='';
                    if ($arribute_price['currencyCode']=='EURO') {
                        $currency='EUR';
                    } else {
                        $currency=$arribute_price['currencyCode'];
                    }
                    $price=$arribute_price['salesPrice'];
                    $from_qty=$arribute_price['minQuantity'];
                    $to_qty=$arribute_price['maxQuantity'];
                    $unitMeasureCode=$arribute_price['unitMeasureCode'];
                    $model_load = $this->pricinglistfact->create()->load();
                    $model_load->setData('itemcode', $itemcode);
                    $model_load->setData('pricelist', $pricelist);
                    $model_load->setData('price', $price);
                    $model_load->setData('from_qty', $from_qty);
                    $model_load->setData('to_qty', $to_qty);
                    $model_load->setData('currency', $currency);
                    $model_load->setData('unitmeasurecode', $unitMeasureCode);
                    $model_load->save();
                }
            }
        }
    }
    public function getRetailPrice($item_name, $currency = 'GBP')
    {
        $array = [];
        $result_retails = [];
        $retail='pricing/retail?itemName='.$item_name;
        $itemName_api_result=$this->getCurlData($retail);
        if (isset($itemName_api_result['results']['data'])) {
            $result_retails=$itemName_api_result['results']['data'];
            if (count($result_retails)>0) {
                foreach ($result_retails as $key => $_retail_items) {
                    $arribute_retails=$_retail_items['attributes'];
                    $currency_s='';
                    if ($arribute_retails['currencyCode']=='EURO') {
                        $currency_s='EUR';
                    } else {
                        $currency_s=$arribute_retails['currencyCode'];
                    }
                    if ($arribute_retails['currencyCode']==$currency) {
                        $array['base_price'] = $arribute_retails['retailPriceRate'];
                        $array['suggested_price'] = $arribute_retails['suggestedRetailPriceRate'];
                        break;
                    }
                }
            }
        } else {
            $array=['base_price'=>0,'suggested_price'=>0];
        }
        return $array;
    }
    public function getWholesalePrice($item_name, $currency = 'GBP')
    {
        $array = [];
        $result_retails = [];
        $retail='pricing/wholesale?itemName='.$item_name;
        $itemName_api_result=$this->getCurlData($retail);

        if (isset($itemName_api_result['results']['data'])) {
            $result_retails=$itemName_api_result['results']['data'];
            if (count($result_retails)>0) {
                foreach ($result_retails as $key => $_retail_items) {
                    $arribute_retails=$_retail_items['attributes'];
                    $currency_s='';
                    if ($arribute_retails['currencyCode']=='EURO') {
                        $currency_s='EUR';
                    } else {
                        $currency_s=$arribute_retails['currencyCode'];
                    }
                    if ($arribute_retails['currencyCode']==$currency) {
                        $array['wholesale_price'] = $arribute_retails['wholesalePriceRate'];
                        break;
                    }
                }
            }
        } else {
            $array=['wholesale_price'=>0];
        }
        return $array;
    }
    public function getSpecialPriceItem($item_code, $currency)
    {
        $array = [];
        $result_retails = [];
        $exist_product_in_magento =$this->checkProductExistByItemCode($item_code);

        $product = $this->_product->create()->setStoreId(0)->load($exist_product_in_magento);

        $measureUnitCode = $product->getData('is_unitmeasurecode');

        $special  = "inventory/specialprice?itemCode=$item_code&currencyCode=$currency";
        $itemName_api_result=$this->getCurlData($special);
        if (isset($itemName_api_result['results']['data'])) {
            $result_retails=$itemName_api_result['results']['data'];
        }


        if (count($result_retails)>0) {
            foreach ($result_retails as $key => $_retail_items) {
                $arribute_retails=$_retail_items['attributes'];
                $currency_s='';
                if ($arribute_retails['currencyCode']=='EURO') {
                    $currency_s='EUR';
                } else {
                    $currency_s=$arribute_retails['currencyCode'];
                }
                if ($arribute_retails['currencyCode']==$currency && strtolower((string)$measureUnitCode)==strtolower((string)$arribute_retails['unitMeasureCode'])) {
                    $array['special_price'] = $arribute_retails['specialPrice'];
                        if(isset($arribute_retails['dateFrom']) && isset($arribute_retails['dateTo'])){
//                            $dateFromArray = explode("-",substr($arribute_retails['dateFrom'],0,10));
//                            $newFromDate = $dateFromArray[1].'/'.$dateFromArray[2].'/'.$dateFromArray[0];
//                            $fromdate = date('m/d/Y', strtotime($newFromDate));
//
//                            $dateToArray = explode("-",substr($arribute_retails['dateTo'],0,10));
//                            $newToDate = $dateToArray[1].'/'.$dateToArray[2].'/'.$dateToArray[0];
//                            $todate = date('m/d/Y', strtotime($newToDate));
                            $limitDate = strtotime('2120/01/01 00:00:00');
                            if(strtotime($arribute_retails['dateTo']) > $limitDate)
                                $todate = date('Y/m/d H:i:s',$limitDate);
                            else
                                $todate = date('Y/m/d H:i:s',strtotime($arribute_retails['dateTo']));

                            $fromdate = date('Y/m/d H:i:s',strtotime($arribute_retails['dateFrom']));

                            $array['dateFrom'] = $fromdate;
                            $array['dateTo'] =$todate;
                        }
                    break;
                }
            }
        }
        return $array;
    }
}
