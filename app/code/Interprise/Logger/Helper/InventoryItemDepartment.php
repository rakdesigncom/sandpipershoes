<?php

/**
 * Created by PhpStorm.
 * User: Shadab
 * Date: 7/8/2018
 * Time: 10:02 AM
 */

namespace Interprise\Logger\Helper;

use \Interprise\Logger\Helper\Data;
use Magento\Setup\Exception;
use Magento\UrlRewrite\Model\Exception\UrlAlreadyExistsException;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\DB\Adapter\DuplicateException;
use Magento\Catalog\Api\SpecialPriceInterface;
use Magento\Catalog\Api\Data\SpecialPriceInterfaceFactory;


class InventoryItemDepartment extends Data
{

    public $_prices;
    public $category;
    public $productvisibility;
    public $objectManager;
    public $directory;
    public $attribute_repository;
    protected $_file;
    private $specialPrice;
    private $specialPriceFactory;
    private $_categoryLinkManagementInterface;
    public $helper_inventoryitem;
    public $helper_inventorymatrixitem;

    protected $productFactory;
    protected $resource;
    protected $connection;
    protected $appState;

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
        //\Interprise\Logger\Model\InstallwizardFactory $installwizardFactory,
        \Interprise\Logger\Model\ResourceModel\Installwizard\CollectionFactory $installwizardFactory,
        \Interprise\Logger\Model\ShippingstoreinterpriseFactory $shippingstoreinterpriseFactory,
        \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory,
        \Magento\Framework\App\ResourceConnection $resourceCon,
        \Interprise\Logger\Helper\InventoryItem $inventoryitem,
        \Interprise\Logger\Helper\InventoryMatrixItem $inventoryMatrixitem,
        SpecialPriceInterface $specialPrice,
        SpecialPriceInterfaceFactory $specialPriceFactory,
        \Magento\Framework\App\State $appState
    ) {

        $this->_product = $product;
        $this->productFactory = $productFactory;
        //$this->directory = $this->objectManager->get('\Magento\Framework\Filesystem\DirectoryList');
        $this->resource = $resourceCon;
        $this->connection = $this->resource->getConnection();
        $this->helper_inventoryitem = $inventoryitem;
        $this->helper_inventorymatrixitem = $inventoryMatrixitem;
        $this->appState = $appState;

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

    public function inventoryItemDepartmentSingle($data)
    {
       ini_set("display_errors","1");
       $dataId = $data['DataId'];
       $visibility = 4;
       $update_data['ActivityTime'] = $this->getCurrentTime();
       $product_found = $this->checkProductExistByItemCode($dataId);
       if ($product_found) {
            if(!$this->checkDepartmentFromInterprise($dataId)){
                $status = $this->disableMagentoItem($product_found);
                if($status['Status']){
                    $update_data['Status'] = 'Success';
                    $update_data['Response'] = "Magento Item ".$product_found." disabled because department code (suave) not assigned on Interprise.";
                    $update_data['Remarks'] = "Magento Item ".$product_found." disabled because department code (suave) not assigned on Interprise.";
                } else{
                    $update_data['Status'] = 'Fail';
                    $update_data['Response'] = $status['Remarks'];
                    $update_data['Remarks'] = $status['Remarks'];
                }
            } else{
                $update_data['Status'] = 'Success';
                $update_data['Response'] = "Product ".$product_found." already exists on Magento";
                $update_data['Remarks'] = "Product ".$product_found." already exists on Magento";
            }
        } else{
            $api_responsc = $this->getCurlData('inventory/'.$dataId);

            if (isset($api_responsc['results']['data']) && $api_responsc['api_error']) {
                echo '<br/>'.$itemType = strtolower($api_responsc['results']['data']['attributes']['itemType']);
                if($itemType == 'matrix group'){

                    $status = $this->helper_inventorymatrixitem->InventoryMatrixItem_single($data);
                    echo '<pre>';
                    print_r($status);
                    $update_data = array_merge($update_data, $status);
                } else{
                    $allowedType = ['stock', 'non-stock', 'service', 'note', 'matrix item'];
                    if (in_array($itemType, $allowedType)) {
                        if($itemType=='matrix item')
                            $visibility = 1;
                        $status = $this->helper_inventoryitem->inventoryItemSingle($data, $visibility);
                        $update_data = array_merge($update_data, $status);
                    } else {
                        $errMsg = 'This is the not allowed item type allowed types are ';
                        $update_data['Status'] = 'Not allowed';
                        $update_data['Remarks'] = $errMsg . implode('|', $allowedType);
                    }
                }
            } else {
                $update_data['Status'] = 'Fail';
                $update_data['Response'] = json_encode($api_responsc);
                $update_data['Remarks'] = 'No response from inventory/' . $dataId;
            }
        }
        print_r($update_data);
        return $update_data;
    }

    public function disableMagentoItem($productId){
        try{
            $set_product = $this->productfactory->create()->setStoreId(0)->load($productId);
            $set_product->setStatus(2);

            $set_product->save();
            $update_data['Status'] = true;
            $update_data['Remarks'] = "";
            return $update_data;
        } catch(Exception $e){
            $err_msg = $e->getMessage();
            $update_data['Status'] = false;
            $update_data['Remarks'] = "Error - " . $err_msg . " in function " . __METHOD__;
            return $update_data;
        }
    }
}
