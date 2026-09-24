<?php
/**
 * Created by PhpStorm.
 * User: mm
 * Date: 7/8/2018
 * Time: 10:02 AM
 */

namespace Interprise\Logger\Helper;

use \Interprise\Logger\Helper\Data;
use Magento\Framework\App\Helper\Context;
use Magento\Setup\Exception;

class InventoryStock extends Data
{
    public $_prices;

    protected $_stockRegistry;

    protected $prices;

    protected $warehouseCode;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Catalog\Model\ProductFactory $product,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categorycollection,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
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
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Interprise\Logger\Helper\Prices $_prices
    ) {
        $this->_product = $product;
        $this->_stockRegistry = $stockRegistry;
        $this->prices = $_prices;
        $this->warehouseCode = ['MAIN', 'RESERVE'];
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

    public function inventoryStockSingle($data)
    {
        $dataId = $data['DataId'];
        $exist_product_in_magento = $this->checkProductExistByItemCode($dataId);
        $update_data['ActivityTime'] = $this->getCurrentTime();
        $update_data['Request'] = "Check Product in Magento";
        $update_data['Response'] = "Product not found in Magento";
        if (!$exist_product_in_magento) {
            $update_data['Status'] = 'Fail';
            $update_data['Remarks'] = 'Product not found in Magento to update Stock';

            return $update_data;
        }
        $item_stock_restult = $this->getItemStockFromEndpoint($dataId);
        // echo '<pre>$item_stock_restult';
        // print_r($item_stock_restult);
        $update_data['Request'] = $item_stock_restult['request'];
        $update_data['Response'] = $item_stock_restult['response'];
        if ($item_stock_restult['Status']) {
            $stock_availability = $item_stock_restult['entity_id'];
            $product_id = $exist_product_in_magento;
            $result_update = $this->updateProductStock($product_id, $stock_availability);
            if ($result_update['Status']) {
                $update_data['Status'] = 'Success';
                $update_data['Remarks'] = 'Success';
            } else {
                $update_data['Status'] = 'Fail';
                $update_data['Remarks'] = $result_update['error'];
            }
        } else {
            $update_data['Status'] = 'Fail';
            $update_data['Remarks'] = $item_stock_restult['error'];
        }

        return $update_data;
    }

    public function getItemStockFromEndpoint($item_code)
    {
        $api_responsc = $this->getCurlData("inventory/$item_code/StockQuantity");
        if ($api_responsc['api_error']) {
            $result = $api_responsc['results']['data'];
            $final_data = [];
            if (count($result) > 0) {
                foreach ($result as $key => $value) {
                    $warehouseCode = $value['attributes']['warehouseCode'];
                    $stock = $value['attributes']['normalUnitsInStock'] - $value['attributes']['unitsAllocated'];
                    $final_data[$warehouseCode] = ['warehouseCode' => $warehouseCode, 'potentialStock' => $stock];
                }
            }
            // echo '<pre>';
            // print_r($final_data);
            if (count($final_data) > 0) {
                $stock = 0;
                foreach ($final_data as $code_key => $_code) {
                    $stock = $stock + $_code['potentialStock'];
                }
            } else {
                $stock = 0;
            }
            $status['Status'] = true;
            $status['error'] = "";
            $status['entity_id'] = $stock;
            $status['request'] = $api_responsc['request'];
            $status['response'] = json_encode($api_responsc['results']['data']);

            return $status;
        } else {
            $err_msg = json_encode($api_responsc['results']);
            $status['Status'] = false;
            $status['error'] = "Err msg - " . $err_msg . " in function " . __METHOD__;
            $status['entity_id'] = '';
            $status['request'] = $api_responsc['request'];
            $status['response'] = json_encode($api_responsc['results']);

            return $status;
        }
    }


}
