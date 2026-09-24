<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Interprise\Logger\Helper;

use \Interprise\Logger\Helper\Data;
use Magento\Framework\App\Helper\Context;
use Magento\Setup\Exception;

/**
 * Description of InventoryPricingLevel
 *
 * @author Shadab
 */
class InventoryPricingLevel extends Data
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

    public function inventoryPricingLevelSingle($data)
    {
        $dataId = $data['DataId'];
        $exist_product_in_magento = $this->checkProductExistByItemCode($dataId);
        $update_data['ActivityTime'] = $this->getCurrentTime();
        $update_data['Request'] = "Check Product in Magento";
        $update_data['Response'] = "Product not found in Magento";
        if (!$exist_product_in_magento) {
            $update_data['Status'] = 'Fail';
            $update_data['Remarks'] = 'Product not found in Magento to update PriceLevel';

            return $update_data;
        }
        $restuls = $this->prices->interprise_price_lists($dataId);
        if ($restuls['Status']) {
            $update_data['Request'] = "Pricelist update";
            $update_data['Response'] = 'Pricelist updated';
            $update_data['Status'] = 'Success';
            $update_data['Remarks'] = 'Success';
        } else {
            $update_data['Request'] = "Pricelist update";
            $update_data['Response'] = 'Pricelist update failed';
            $update_data['Status'] = 'Fail';
            $update_data['Remarks'] = $restuls['error'];
        }

        return $update_data;
    }
}
