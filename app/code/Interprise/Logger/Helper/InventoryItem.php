<?php

/**
 * Created by PhpStorm.
 * User: Shadab
 * Date: 7/8/2018
 * Time: 10:02 AM
 */

namespace Interprise\Logger\Helper;

use \Interprise\Logger\Helper\Data;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Setup\Exception;
use Magento\UrlRewrite\Model\Exception\UrlAlreadyExistsException;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\DB\Adapter\DuplicateException;
use Magento\Catalog\Api\SpecialPriceInterface;
use Magento\Catalog\Api\Data\SpecialPriceInterfaceFactory;

class InventoryItem extends Data
{

    public $_prices;

    public $category;

    public $productvisibility;

    public $objectManager;

    public $directory;

    public $attribute_repository;

    protected $_file;

    protected $inventoryStock;

    protected $prices;

    protected $processor;

    protected $productGallery;

    protected $eavConfig;

    protected $attributeOptionManagementInterface;

    protected $attributeOptionLabelFactory;

    protected $attributeOptionFactory;

    protected $_directoryList;

    protected $_io;

    protected $filesystem;

    protected $productFactory;

    protected $productLinks;

    protected $resource;

    protected $connection;

    protected $appState;

    private $specialPrice;

    private $specialPriceFactory;

    private $_categoryLinkManagementInterface;

    private $categoryLinkRepository;

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
        //\Interprise\Logger\Model\InstallwizardFactory $installwizardFactory,
        \Interprise\Logger\Model\ResourceModel\Installwizard\CollectionFactory $installwizardFactory,
        \Interprise\Logger\Model\ShippingstoreinterpriseFactory $shippingstoreinterpriseFactory,
        \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory,
        \Interprise\Logger\Helper\InventoryStock $inventorystock,
        \Interprise\Logger\Helper\Prices $_prices,
        \Interprise\Logger\Helper\Category $_category,
        \Magento\Framework\Filesystem\DirectoryList $_directory,
        \Magento\Catalog\Model\Product\Gallery\Processor $gallery,
        \Magento\Catalog\Model\ResourceModel\Product\Gallery $productGallery,
        \Magento\Eav\Api\AttributeRepositoryInterface $attribute_repository,
        \Magento\Eav\Model\Config $_eavConfig,
        \Magento\Eav\Api\AttributeOptionManagementInterface $attributeOptionManagementInterface,
        \Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory $attributeOptionLabelFactory,
        \Magento\Eav\Api\Data\AttributeOptionInterfaceFactory $attributeOptionFactory,
        \Magento\Framework\Filesystem\Driver\File $file,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem\Io\File $io,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\App\ResourceConnection $resourceCon,
        \Magento\Catalog\Api\Data\ProductLinkInterfaceFactory $links,
        SpecialPriceInterface $specialPrice,
        SpecialPriceInterfaceFactory $specialPriceFactory,
        \Magento\Catalog\Api\CategoryLinkManagementInterface $categoryLinkManagementInterface,
        \Magento\Catalog\Api\CategoryLinkRepositoryInterface $categoryLinkRepositoryInterface,
        \Magento\Framework\App\State $appState
    ) {
        $this->inventoryStock = $inventorystock;
        $this->prices = $_prices;
        $this->category = $_category;
        //$this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->directory = $_directory;
        $this->_product = $product;
        $this->processor = $gallery;
        $this->productGallery = $productGallery;
        $this->attribute_repository = $attribute_repository;
        $this->eavConfig = $_eavConfig;
        $this->attributeOptionManagementInterface = $attributeOptionManagementInterface;
        $this->attributeOptionLabelFactory = $attributeOptionLabelFactory;
        $this->attributeOptionFactory = $attributeOptionFactory;
        $this->_file = $file;
        $this->_directoryList = $directoryList;
        $this->_io = $io;
        $this->filesystem = $filesystem;
        $this->productFactory = $productFactory;
        $this->productLinks = $links;
        //$this->directory = $this->objectManager->get('\Magento\Framework\Filesystem\DirectoryList');
        $this->resource = $resourceCon;
        $this->connection = $this->resource->getConnection();
        $this->specialPrice = $specialPrice;
        $this->specialPriceFactory = $specialPriceFactory;
        $this->_categoryLinkManagementInterface = $categoryLinkManagementInterface;
        $this->categoryLinkRepository = $categoryLinkRepositoryInterface;
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

    public function inventoryItemSingle($data, $visibility = 1)
    {
        ini_set("display_errors", "1");
        $this->productvisibility = $visibility;
        $dataId = $data['DataId'];
        //$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        //$directory = $this->objectManager->get('\Magento\Framework\Filesystem\DirectoryList');
        $media_path = $this->directory->getRoot() . '/pub/media/isimages/';
        if (!defined('UPLOAD_DIR')) {
            define('UPLOAD_DIR', $media_path);
        }

        if ($this->checkDepartmentFromInterprise($dataId)) {
            //if(1==1){
            if (!$this->checkProductExistByItemCode($dataId)) {
                $update_data = $this->insertItemNew($dataId, $visibility);
            } else {
                try {
                    $api_responsc = $this->getCurlData('inventory/all?itemcode=' . $dataId);
                    if ($api_responsc['results']['data'] && $api_responsc['api_error']) {
                        $update_data['ActivityTime'] = $this->getCurrentTime();
                        $update_data['Request'] = $api_responsc['request'];
                        $update_data['Response'] = json_encode($api_responsc['results']['data']);
                        if ($api_responsc['api_error'] && isset($api_responsc['results']['data'])) {
                            $attribute_data_items = $api_responsc['results']['data']['attributes']['item'];
                            echo '<br/>' . $itemtype = strtolower($attribute_data_items['itemType']);
                            $allowedType = ['stock', 'non-stock', 'service', 'note', 'matrix item'];
                            if (in_array($itemtype, $allowedType)) {
                                print_r(['CREATEPRODUCTFUNC']);
                                $status = $this->createProduct($api_responsc, $visibility);
                                $update_data = array_merge($update_data, $status);
                            } else {
                                print_r(['CREATEPRODUCTFUNC SKIP']);
                                $errMsg = 'This is the not allowed item type allowed types are ';
                                $update_data['Status'] = 'Not allowed';
                                $update_data['Remarks'] = $errMsg . implode('|', $allowedType);
                                $update_data['TypeProduct'] = $itemtype;
                            }
                        } else {
                            $update_data['Status'] = 'Fail';
                            $update_data['Remarks'] = $api_responsc['results'];
                        }
                    } else {
                        $update_data['Status'] = 'Fail';
                        $update_data['Remarks'] = 'No response from inventory/' . $dataId;
                    }
                } catch (Exception $ex) {
                    $err_msg = $ex->getMessage();
                    $update_data['Status'] = 'Fail';
                    $update_data['Remarks'] = 'In method ' . __METHOD__ . ' ' . $err_msg;
                }
            }
        } else {
            $update_data['Status'] = 'Department not matched';
            $update_data['Response'] = '';
            $update_data['ActivityTime'] = $this->getCurrentTime();
            $update_data['Remarks'] = json_encode('Department does not match');
        }
        $this->prf($update_data);

        return $update_data;
    }

    public function insertItemNew($dataId)
    {

        echo '<br/>Method: ' . __METHOD__ . '<br/>';
        try {
            $media_path = $this->directory->getRoot() . '/pub/media/isimages/';
            if (!defined('UPLOAD_DIR')) {
                define('UPLOAD_DIR', $media_path);
            }
            $api_response = $this->getCurlData('inventory/all?itemcode=' . $dataId);
            $api_response_web = $this->getCurlData('inventory/weboption/' . $dataId . '/description');

            //$this->prf($api_response_web);
            $update_data['ActivityTime'] = $this->getCurrentTime();
            $update_data['Request'] = $api_response['request'];
            $update_data['Response'] = 'NA';

            if ($api_response['api_error'] && isset($api_response['results']['data'])) {
                $update_data['Request'] = $api_response['request'];
                $update_data['Response'] = json_encode($api_response['results']['data']);
                $data = $api_response['results']['data']['attributes'];
                echo '<br/>' . $itemtype = strtolower($data['item']['itemType']);
                $allowedType = ['stock', 'non-stock', 'service', 'note', 'matrix item'];
                if (in_array($itemtype, $allowedType)) {
                    if (!isset($data)) {
                        $update_data['Status'] = false;
                        $errMsg = 'Attribute data not found in API response for dataid ';
                        $update_data['Remarks'] = $errMsg . $dataId;
                    } else {
                        if ($data['item']['status'] == "D") {
                            $update_data['Status'] = 'Discontinued';
                            $errMsg = 'Item Discontinued!! ';
                            $update_data['Remarks'] = $errMsg . $dataId;

                            print_r(json_encode(["ITEM_SKIP" => $data]) . "\n");
//                            file_put_contents("/var/www/html/var/interprise.log", json_encode(["ITEM_SKIP" => $data]) . "\n", FILE_APPEND);
                            return $update_data;
                        }

                        print_r(json_encode(["INSERT_ITEM" => $data]) . "\n");
//                        file_put_contents("/var/www/html/var/interprise.log", json_encode(["INSERT_ITEM" => $data]) . "\n", FILE_APPEND);
                        $product_name = $data['description'][0]['itemDescription'];
                        $product_description = $product_name;

                        if (isset($data['description'][0]['extendedDescription'])) {
                            $product_description = $data['description'][0]['extendedDescription'];
                        }
                        $seTitle = $product_name;
                        $seKeywords = $product_name;
                        $seDescription = $product_description;
                        $webDescription = $product_description;

                        if ($api_response_web['api_error']) {
                            $webSiteCode = 'WEB-000001';
                            ////////////////////////// edited by manisha to check websitecode /////////////////////////
                            foreach ($api_response_web['results']['data'] as $api_web_data) {
                                if ($api_web_data['attributes']['webSiteCode'] == $webSiteCode) {
                                    $data_web = $api_web_data['attributes'];
                                }
                            }

                            if (isset($data_web)) {
                                if (isset($data_web['seTitle'])) {
                                    $seTitle = $data_web['seTitle'];
                                }

                                if (isset($data_web['seKeywords'])) {
                                    $seKeywords = $data_web['seKeywords'];
                                }

                                if (isset($data_web['seDescription'])) {
                                    $seDescription = $data_web['seDescription'];
                                }

                                if (isset($data_web['webDescription'])) {
                                    $webDescription = $data_web['webDescription'];
                                }
                            }
                        }

                        /////////////////////////// Edited By Manisha ////////////////////////////
                        // to get value of isfeatured and published according to websitecode ///////
                        $isFeatured = '';
                        $isPublished = 1;
                        $api_response_web_new = $this->getCurlData('inventory/weboption/' . $dataId);
                        if ($api_response_web_new['api_error']) {
                            foreach ($api_response_web_new['results']['data'] as $api_web_data) {
                                if ($api_web_data['attributes']['webSiteCode'] == $webSiteCode) {
                                    $data_web = $api_web_data['attributes'];
                                    if (isset($data_web['isFeatured'])) {
                                        $isFeatured = $data_web['isFeatured'];
                                    }
                                    if (isset($data_web['published'])) {
                                        $isPublished = $data_web['published'];
                                    }
                                }
                            }
                        }
                        ///////////////////////////////////////////////////// End Code ////////////////////////////////////////////////////////////////////////

                        $product_sku = $data['itemName'];
                        try {
                            $productRepository = ObjectManager::getInstance()->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
                            $set_product = $productRepository->get($product_sku, true, null, true);
                        } catch (NoSuchEntityException) {
                            $set_product = $this->productFactory->create()->setStoreId(0);
                        }

                        print_r(json_encode(["INSERT_ITEM_ID" => $set_product->getId()]) . "\n");
//                        file_put_contents("/var/www/html/var/interprise.log", json_encode(["INSERT_ITEM_ID" => $set_product->getId()]) . "\n", FILE_APPEND);
                        $isitemcode = $data['itemCode'];
                        $set_product->setMetaTitle($seTitle);
                        $set_product->setMetaKeyword($seKeywords);
                        $set_product->setMetaDescription($product_description);
                        $set_product->setDescription($webDescription);
//                      $set_product->setShortDescription($seDescription);
                        $prices = $this->getAllPricesNew($data, $product_sku, $isitemcode, 'GBP');
                        try {
                            if (strlen($product_sku) > 64) {
                                $errMsg = 'Sku length greater than 64 characters';

                                $update_data['Status'] = 'Fail';
                                $update_data['Remarks'] = "Err msg1 - " . $errMsg . " in function " . __METHOD__;

                                return $update_data;
                            }
                            $set_product->setWebsiteIds([1, 2]);
                            echo "<br>here<br>";
                            $set_product->setStoreId(0);
                            $set_product->setAttributeSetId(4);
                            $set_product->setTypeId('simple');
                            $set_product->setCreatedAt(strtotime('now'));
                            // time of product creation
                            //$set_product->setName($product_name);
                            $set_product->setName($product_sku);
                            // add Name of Product

                            /////////////////testing////////////////
                            //$newSKU = str_replace("/", "_", $product_sku);

                            ///////////////////////////////////
                            $set_product->setSku($product_sku);
                            // add Interprise itemcode
                            $set_product->setInterpriseItemCode($isitemcode);
                            $set_product->setInterpriseStatus($data['item']['status']);
                            // add sku hear

                            $set_product->setIsfeatured($isFeatured);
                            $set_product->setPublished($isPublished);

                            $set_product->setData('ext_description', $product_description);
                            if (isset($data['unitOfMeasure'][0]['weightInKilograms']) && $data['unitOfMeasure'][0]['weightInKilograms'] > 0) {
                                $set_product->setWeight($data['unitOfMeasure'][0]['weightInKilograms']);
                            } else {
                                $set_product->setProductHasWeight(0);
                                $set_product->setWeight(0);
                            }

                            $set_product->setData('interprise_item_type', $data['item']['itemType']);
                            if (strtolower($set_product->getData('interprise_item_type')) != 'matrix item') {
                                $set_product->setName($product_name);
                            }

                            $unitmeasurecode = $data['unitOfMeasure'][0]['unitMeasureCode'];
                            $set_product->setData('is_unitmeasurecode', $unitmeasurecode);

                            if (isset($data['unitOfMeasure'][0]['upcCode'])) {
                                $set_product->setUpccode($data['unitOfMeasure'][0]['upcCode']);
                            }

                            $mfgcode = "";
                            if (isset($data['item']['manufacturerCode'])) {
                                $set_product->setManufacturercode($data['item']['manufacturerCode']);
                                $mfgcode = $data['item']['manufacturerCode'];
                            }

                            if ($data['item']['status'] == "D") {
                                $set_product->setStatus(2);
                            } else {
                                if ($isPublished == '1') {
                                    $set_product->setStatus(1);
                                } else {
                                    $set_product->setStatus(2);
                                }
                            }
                            $set_product->setInterpriseStatus($data['item']['status']);

                            if (isset($data['category'])) {
                                $categories = $data['category'];
                                $category_ids = [];
                                foreach ($categories as $key => $value) {
                                    $catExisting = $this->checkCategoryExistByIsCode($value["categoryCode"]);
                                    if (!$catExisting) {
//                                        file_put_contents("/var/www/html/var/interprise.log", json_encode(['notFoundCategory' => $value]) . "\n", FILE_APPEND);
                                        print_r(['notFoundCategory' => $value]);
                                    }
                                    $category_ids = array_merge($category_ids, $catExisting);
                                }
                            }
                            if (!isset($category_ids) || !$category_ids || $category_ids[0] == '') {
                                $parent_id_default = $this->getConfig('setup/general/defaultcategory');
                                $category_ids = [$parent_id_default];
                            }

//                            $set_product->setCategoryIds($category_ids);
                            // Product Category
                            $set_product->setTaxClassId(2);

                            if ($data['item']['salesTaxOption'] == 'Customer') {
                                $set_product->setData('is_vatexempt', 1);
                            } else {
                                $set_product->setData('is_vatexempt', 0);
                            }

                            // type of tax class
                            // (0 - none, 1 - default, 2 - taxable, 4 - shipping)
                            $set_product->setVisibility($this->productvisibility);
                            // catalog and search visibility
                            //  if(isset($prices['dateTo']) && $prices['dateTo']!=''){
                            //$set_product->setNewsToDate($prices['dateTo']);
                            //}
                            // product set as new from
                            // add image path hear
                            // $set_product->setImage('/testimg/test.jpg');
                            // add small image path hear
                            // $set_product->setSmallImage('/testimg/test.jpg');
                            // add Thumbnail image path hear
                            // $set_product->setThumbnail('/testimg/test.jpg');
                            // product set as new to
                            $set_product->setCountryOfManufacture('UK');
                            // country of manufacture (2-letter country code)

                            $set_product->setPrice($prices['price']);
                            $set_product->setData('is_retailprice', $prices['retail_price']);
                            $is_specialPrice = ['special_price' => $prices['special_price'], 'date_from' => $prices['dateFrom'], 'date_to' => $prices['dateTo']];
                            $set_product->setData('is_specialprice', json_encode($is_specialPrice));
                            $set_product->setData('is_wholesaleprice', $prices['wholesale_price']);
                            // price in form 100.99
                            // $set_product->setCost(88.33);
                            // price in form 88.33
//                            if (isset($prices['special_price']) && $prices['special_price'] != '') {
//                                $set_product->setSpecialPrice($prices['special_price']);
//                            } else {
//                                $set_product->setSpecialPrice('');
//                            }
//                            // special price in form 99.85
//                            if (isset($prices['dateFrom']) && $prices['dateFrom'] != '') {
//                                $set_product->setSpecialFromDate($prices['dateFrom']);
//                            }
//                            // special price from (MM-DD-YYYY)
//                            if (isset($prices['dateTo']) && $prices['dateTo'] != '') {
//                                $set_product->setSpecialToDate($prices['dateTo']);
//                            }
                            // special price to (MM-DD-YYYY)
                            $set_product->setMsrpEnabled(1);
                            // enable MAP
                            $set_product->setMsrpDisplayActualPriceType(1);
                            // display actual price
                            // (1 - on gesture, 2 - in cart, 3 - before order confirmation, 4 - use config)
                            $set_product->setMsrp($prices['msrp']);

                            ////////////////////// added for custom fields ///////////////////////////////////////

                            //echo "<br/>Inside Insert";
                            $custom_fields = ['webid_c'];

                            $custom_fields_dropdown = ['flatscreendimensions_c', 'aspectratio_c', 'nativeresolution_c', 'resolution_c', 'displaytype_c', 'physicalscreensize_c', 'projectormodel_c', 'flatscreensize_c'];

                            if (isset($data['item']['customFields']) && count($data['item']['customFields']) > 0) {
                                foreach ($data['item']['customFields'] as $key => $item) {
                                    if (in_array(strtolower($item['field']), $custom_fields)) {
                                        $set_product->setData(strtolower($item['field']), $item['value']);

                                        if (strtolower($item['field']) == 'projectormanufacturer_c') {
                                            $attributeCode = 'projector_manufacturer';
                                            $attributeValue = trim($item['value']);
                                            $attribute = $this->eavConfig->getAttribute('catalog_product', $attributeCode);
                                            $optionID = $attribute->getSource()->getOptionId($attributeValue);

                                            if (isset($optionID) && $optionID != '') {
                                                //echo "<br/>In If";
                                                $set_product->setData($attributeCode, $optionID);
                                            } else {
                                                //echo "<br/>In Else";
                                                $attributeID = $attribute->getId();

                                                $attributeOptionManagement = $this->attributeOptionManagementInterface;
                                                $optionLabelFactory = $this->attributeOptionLabelFactory;
                                                $optionFactory = $this->attributeOptionFactory;

                                                $optionLabel = $optionLabelFactory->create();
                                                $optionLabel->setStoreId(0);
                                                $optionLabel->setLabel($attributeValue);

//                                                $optionLabel1 = $optionLabelFactory->create();
                                                //                                              $optionLabel1->setStoreId(1);
                                                //                                            $optionLabel1->setLabel($attributeValue);

                                                $option = $optionFactory->create();
                                                $option->setLabel($attributeValue);
                                                $option->setStoreLabels([$optionLabel]);
                                                $option->setSortOrder(0);
                                                $option->setIsDefault(false);

                                                $attributeOptionManagement->add(
                                                    'catalog_product',
                                                    $attributeCode,
                                                    $option
                                                );
                                                $attribute = $this->eavConfig->getAttribute('catalog_product', $attributeCode);
                                                $optionID = $attribute->getSource()->getOptionId($attributeValue);
                                                $set_product->setData($attributeCode, $optionID);
                                            }
                                        }
                                    } else if (in_array(strtolower($item['field']), $custom_fields_dropdown)) {
                                        //$eavConfig = $objectManager->get('\Magento\Eav\Model\Config');
                                        $attributeCode = strtolower(trim($item['field']));
                                        $attributeValue = trim($item['value']);
                                        $attribute = $this->eavConfig->getAttribute('catalog_product', $attributeCode);
                                        $optionID = $attribute->getSource()->getOptionId($attributeValue);
                                        if (isset($optionID) && $optionID != '') {
                                            //echo "<br/>In If";
                                            $set_product->setData($attributeCode, $optionID);
                                        } else {
                                            //echo "<br/>In Else";
                                            $attributeID = $attribute->getId();
                                            //$attributeOptionManagement =  $objectManager->get('\Magento\Eav\Api\AttributeOptionManagementInterface');
                                            //$optionLabelFactory = $objectManager->get('\Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory');
                                            //$optionFactory = $objectManager->get('\Magento\Eav\Api\Data\AttributeOptionInterfaceFactory');

                                            $attributeOptionManagement = $this->attributeOptionManagementInterface;
                                            $optionLabelFactory = $this->attributeOptionLabelFactory;
                                            $optionFactory = $this->attributeOptionFactory;

                                            $optionLabel = $optionLabelFactory->create();
                                            $optionLabel->setStoreId(0);
                                            $optionLabel->setLabel($attributeValue);

                                            //        $optionLabel1 = $optionLabelFactory->create();
                                            //       $optionLabel1->setStoreId(1);
                                            //      $optionLabel1->setLabel($attributeValue);

                                            $option = $optionFactory->create();
                                            $option->setLabel($attributeValue);
                                            $option->setStoreLabels([$optionLabel]);
                                            $option->setSortOrder(0);
                                            $option->setIsDefault(false);

                                            $attributeOptionManagement->add(
                                                'catalog_product',
                                                $attributeCode,
                                                $option
                                            );
                                            $attribute = $this->eavConfig->getAttribute('catalog_product', $attributeCode);
                                            $optionID = $attribute->getSource()->getOptionId($attributeValue);
                                            $set_product->setData($attributeCode, $optionID);
                                        }
                                    }
                                }
                            }

                            /////////////////////// Code to add manufacturercode //////////////////////////////////

                            //$eavConfig = $objectManager->get('\Magento\Eav\Model\Config');
                            //echo '<br/>After Custom Code';
                            echo '<br/>$mfgcode ' . $mfgcode;
                            if (trim($mfgcode) != '') {
                                $attribute = $this->eavConfig->getAttribute('catalog_product', 'manufacturercode');
                                if ($attribute) {
                                    echo '<br/>$optionID ' . $optionID = $attribute->getSource()->getOptionId($mfgcode);
                                    if (isset($optionID) && $optionID != '') {
                                        $set_product->setData('manufacturercode', $optionID);
                                    } else {
                                        echo "<br/>Inside Else";
                                        $attributeID = $attribute->getId();
                                        $attributeOptionManagement = $this->attributeOptionManagementInterface;
                                        $optionLabelFactory = $this->attributeOptionLabelFactory->create();
                                        $optionFactory = $this->attributeOptionFactory->create();
                                        echo '<br/>Level 1';
                                        $optionLabel = $optionLabelFactory;
                                        $optionLabel->setStoreId(0);
                                        $optionLabel->setLabel($mfgcode);
                                        echo '<br/>Level 2';
//                                        $optionLabel1 = $optionLabelFactory;
                                        //                                       $optionLabel1->setStoreId(1);
                                        //                                      $optionLabel1->setLabel($mfgcode);

                                        $option = $optionFactory;
                                        $option->setLabel($mfgcode);
                                        $option->setStoreLabels([$optionLabel]);
                                        $option->setSortOrder(0);
                                        $option->setIsDefault(false);
                                        echo '<br/>Level 3';
                                        $attributeOptionManagement->add(
                                            \Magento\Catalog\Model\Product::ENTITY,
                                            'manufacturercode',
                                            $option
                                        );
                                        echo '<br/>Level 4';
                                        $attribute = $this->eavConfig->getAttribute('catalog_product', 'manufacturercode');
                                        echo '<br/>$optionID ' . $optionID = $attribute->getSource()->getOptionId($mfgcode);
                                        $set_product->setData('manufacturercode', $optionID);
                                    }
                                }
                            }
                            //////////////////////////// end code //////////////////////////////////////////
                            //////////////////////////// end code custom fields//////////////////////////////

                            echo '<br/>$counter ' . $counter = $data['item']['counter'];
                            echo '<br/>$url' . $url = preg_replace('#[^0-9a-z]+#i', '-', 'p' . '-' . $counter . '-' . $product_name);

                            echo '<br/>' . $url = strtolower($url);
                            try {
                                $set_product->setUrlKey($url);

                                echo '<br/>after url key set';
                            } catch (Exception $ex) {
                                $update_data['Status'] = 'Fail';
                                $update_data['Remarks'] = $ex->getMessage();

                                return false;
                            }
                            // Manufacturer's Suggested Retail Price
                            echo '<br/>before stock';
                            $get_stock_result = $this->getItemStockFromEndpointNew($data);

                            if ($get_stock_result['Status']) {
                                $stock_availability = $get_stock_result['entity_id'];
//                                if ($stock_availability > 0) {
//                                    $is_in_stock = 1;
//                                } else {
//                                    $is_in_stock = 0;
//                                }
                                $is_in_stock = 1;
                                $set_product->setStockData(
                                    [
                                        'use_config_manage_stock' => 1,
                                        // checkbox for 'Use config settings'
                                        'manage_stock' => 1, // manage stock
                                        'min_sale_qty' => 1, // Shopping Cart Minimum Qty Allowed
                                        'max_sale_qty' => 99999, // Shopping Cart Maximum Qty Allowed
                                        'is_in_stock' => $is_in_stock, // Stock Availability of product
                                        'qty' => $stock_availability, // qty of product
                                    ]
                                );
                            }
                            echo '<br/>after stock';
                            $set_product->setJson(json_encode($data));
                            if (!$set_product->getId()) {
                                $set_product->setData('created_at', null);
                            }

                            try {
                                $set_product->save();

                                $this->updateWhosalePrice($set_product);
                            } catch (Exception $ex) {
                                $update_data['Status'] = 'Fail';
                                $update_data['Remarks'] = $ex->getMessage();

                                return false;
                            }
                            if (isset($data['category'])) {
                                $categories = $data['category'];
                                $category_ids = [];
                                foreach ($categories as $key => $value) {
                                    $catExisting = $this->checkCategoryExistByIsCode($value["categoryCode"]);
                                    if (!$catExisting) {
//                                        file_put_contents("/var/www/html/var/interprise.log", json_encode(['notFoundCategory' => $value]) . "\n", FILE_APPEND);
                                        print_r(['notFoundCategory' => $value]);
                                    }
                                    $category_ids = array_merge($category_ids, $catExisting);
                                }
                            }
                            if (!isset($category_ids) || !$category_ids || $category_ids[0] == '') {
                                $parent_id_default = $this->getConfig('setup/general/defaultcategory');
                                $category_ids = [$parent_id_default];
                            }

//                            if ($data['item']['status'] == "D") {
//                                $set_product->setStatus(2);
//                            } else {
//                                if ($isPublished=='1') {
//                                    $set_product->setStatus(1);
//                                } else {
//                                    echo '<br/>Inside else status';
//                                    $set_product->setStatus(2);
//                                }
//                            }

                            $set_product = $this->productFactory->create()->setStoreId(0)->load($set_product->getId());
//                            $set_product->setCategoryIds($category_ids);
//                            $set_product->save();
                            $get_product_id = $set_product->getId();
                            echo "New product id ==" . $set_product->getId();
//                            $image_response = $this->getCurlData('inventory/' . $dataId);
//                            if ($image_response['api_error']) {
//                                $imgSize = ['image', 'thumbnail', 'small_image'];
//                                foreach ($image_response['results']['data'] as $api_image_data) {
//                                    if (isset($api_image_data['photo'])) {
//                                        $img_field = $api_image_data['photo'];
//                                        $img_path = $this->getImages($img_field);
//                                        $product = $this->productFactory->create()->load($set_product->getId());
//
//                                        $this->processor->addImage($product, $img_path, $imgSize, false, false);
//                                        $product->save();
//                                        if ($this->_file->isExists($img_path)) {
//                                            $this->_file->deleteFile($img_path);
//                                        }
//                                    }
//                                }
//                            }
//
//                            if (isset($data['item']['customFields']) && count($data['item']['customFields']) > 0) {
//                                $base_path = "https://www.avpartsmaster.co.uk/images/product";
//                                $custom_fields = ['imageoverridelarge_c', 'imageoverridemedium_c', 'imageoverrideicon_c'];
//                                $sizes = ['large', 'medium', 'icon'];
//                                $i = 0;
//                                foreach ($data['item']['customFields'] as $key => $item) {
//                                    if (in_array(strtolower($item['field']), $custom_fields)) {
//                                        $img_url = $base_path . '/' . $sizes[$i] . '/';
//                                        $img_path = $this->getcustomimages($img_url, $item['value']);
//                                        if ($img_path != false) {
//                                            $this->processor->addImage($set_product, $img_path, $imgSize, false, false);
//                                            $set_product->save();
//                                            if ($this->_file->isExists($img_path)) {
//                                                $this->_file->deleteFile($img_path);
//                                            }
//                                            break;
//                                        }
//                                        $i++;
//                                    }
//                                }
//                            }

                            /*--------------- Product to insert product accessory data ------------------------ */

                            $accessoryData = [];
                            $insData = [];
                            if (isset($data['accessoryItem'])) {
                                $accessoryData = $data['accessoryItem'];
                            }

                            $substituteData = [];
                            $linkDataAll = [];
                            $i = 0;
                            $this->resource->getConnection()->delete('catalog_product_link', "product_id = '" . $get_product_id . "' AND link_type_id IN(1,4)");
                            //$this->resource->getConnection()->delete('lamp_accessory_mapping', "product_id = '".$isitemcode."'");

//                            if(!empty($accessoryData)){
//                                foreach($accessoryData as $accessory){
//                                    $accessoryID = $accessory['accessoryCode'];
//                                    $insData[] = ['product_id' =>$isitemcode, 'accessory_id' => $accessoryID];
//
//                                    $linkedProduct = $this->productfactory->create()->loadByAttribute("interprise_item_code", $accessoryID);
//                                    if($linkedProduct) {
//                                        $linkData = $this->productLinks->create()
//                                            ->setSku($set_product->getSku())
//                                            ->setLinkedProductSku($linkedProduct->getSku())
//                                            ->setLinkType("upsell");
//                                        $linkDataAll[] = $linkData;
//
//                                        //$relatedProductsArranged[$linkedProduct->getId()] = array('position' => $i++);
//                                    }
//
//                                }
//                                $this->resource->getConnection()->insertMultiple('lamp_accessory_mapping', $insData);
//                            }

                            /*------------------- End Code for Product accessory data ------------------------ */

                            /*--------------------------------------- Code to update upsell product ---------------------------------*/

                            if (isset($data['substituteItem'])) {
                                foreach ($data['substituteItem'] as $substituteNote) {
                                    $substituteCode = $substituteNote['substituteCode'];
                                    $linkedProduct = $this->productfactory->create()->setStoreId(0)->loadByAttribute("interprise_item_code", $substituteCode);
                                    if ($linkedProduct) {
                                        $linkData = $this->productLinks->create()
                                            ->setSku($set_product->getSku())
                                            ->setLinkedProductSku($linkedProduct->getSku())
                                            ->setLinkType("related");
                                        $linkDataAll[] = $linkData;
                                        //$relatedProductsArranged[$linkedProduct->getId()] = array('position' => $i++);
                                    }
                                }
                            }

                            if ($linkDataAll) {
                                $set_product->setProductLinks($linkDataAll);
                            }
                            $set_product->save();

                            /*--------------------------------------End Code to update upsell product ---------------------------------*/

                            if ($set_product->getId() && $set_product->getId() > 0) {
                                $update_data['Status'] = 'Success';
                                $update_data['Remarks'] = 'Entity id ' . $set_product->getId() . ' created';
                                $update_data['entity_id'] = $set_product->getId();
                            } else {
                                $err_msg = "Some error occured while creating the product";
                                $update_data['Status'] = 'Fail';
                                $update_data['Remarks'] = "Err msg - " . $err_msg . " in function " . __METHOD__;
                            }
                        } catch (Exception $ex) {
                            $err_msg = $ex->getMessage();
                            $update_data['Status'] = 'Fail';
                            $update_data['Remarks'] = "Err msg - " . $err_msg . " in function 1 " . __METHOD__;
                        } catch (UrlAlreadyExistsException $e1) {
                            $err_msg = $e1->getMessage();
                            $update_data['Status'] = 'Fail';
                            $update_data['Remarks'] = "Err msg1 - " . $err_msg . " in function 2 " . __METHOD__;
                        } catch (AlreadyExistsException $e2) {
                            $err_msg = $e2->getMessage();

                            $update_data['Status'] = 'Fail';
                            $update_data['Remarks'] = "Err msg2 - SKU already exist " . __METHOD__;
                        } catch (Magento\Framework\DB\Adapter\DuplicateException $e3) {
                            $err_msg = $e3->getMessage();
                            $update_data['Status'] = 'Fail';
                            $update_data['Remarks'] = "Err msg3 - " . $err_msg . " in function 4 " . __METHOD__;
                        }
                    }
                } else {
                    $errMsg = 'This is the not allowed item type allowed types are ';
                    $update_data['Status'] = 'Not allowed';
                    $update_data['Remarks'] = $errMsg . implode('|', $allowedType);
                }
            } else {
                $update_data['Status'] = 'Fail';
                $update_data['Remarks'] = $api_response['results'];
            }
        } catch (Exception $ex) {
            $err_msg = $ex->getMessage();
            $update_data['Status'] = 'Fail';
            $update_data['Remarks'] = 'In method ' . __METHOD__ . ' ' . $err_msg;
        }

        return $update_data;
    }

    public function getAllPricesNew($data, $item_name, $itemcode, $currency = 'GBP')
    {
        /*         * *It updtes the interprise_price_lists table for this item code
         * get the wholesale price
         * get the retial price
         * get the special price
         * and then returns array of wholesale price as price,base_price as msrp and special price
         * * */

        $array = [];
        //get the retail price and suggested retail price
        $retail_prices = $this->getRetailPriceNew($data['retailPrice'], $currency);
        //get the wholesale price
        $wholesale_prices = $this->getWholesalePriceNew($data['wholesalePrice'], $currency);

        $special_prices = $this->prices->getSpecialPriceItem($itemcode, $currency);

        $regular_price_field = $this->getConfig('setup/general/regular_price');
        if ($regular_price_field == 'Retail') {
            $array['price'] = $retail_prices['base_price'];
        } else {
            $array['price'] = $wholesale_prices['wholesale_price'];
        }
        $array['retail_price'] = $retail_prices['base_price'];
        $array['wholesale_price'] = $wholesale_prices['wholesale_price'];
        $array['msrp'] = $retail_prices['suggested_price'];
        if (isset($special_prices['special_price'])) {
            $array['special_price'] = $special_prices['special_price'];
        } else {
            $array['special_price'] = '';
        }
        if (isset($special_prices['dateFrom']) && $special_prices['dateFrom'] != '') {
            if (isset($special_prices['dateTo']) && $special_prices['dateTo'] != '') {
                $array['dateFrom'] = $special_prices['dateFrom'];
                $array['dateTo'] = $special_prices['dateTo'];
            } else {
                $array['dateFrom'] = '';
                $array['dateTo'] = '';
            }
        } else {
            $array['dateFrom'] = '';
            $array['dateTo'] = '';
        }

        //$this->interprisePriceLists($itemcode, $currency);

        return $array;
    }

    public function getRetailPriceNew($data, $currency)
    {
        $array = [];
        if (count($data) > 0) {
            foreach ($data as $key => $price) {
                if ($price['currencyCode'] == $currency) {
                    $array['base_price'] = $price['retailPriceRate'];
                    $array['suggested_price'] = $price['suggestedRetailPriceRate'];
                    break;
                }
            }
        }

        return $array;
    }

    public function getWholesalePriceNew($data, $currency)
    {
        $array = [];
        if (count($data) > 0) {
            foreach ($data as $key => $price) {
                if ($price['currencyCode'] == $currency) {
                    $array['wholesale_price'] = $price['wholesalePrice'];
                    break;
                }
            }
        }

        return $array;
    }

    public function getItemStockFromEndpointNew($data)
    {
        if (isset($data['stockQuantity'])) {
            $final_data = [];
            if (count($data['stockQuantity']) > 0) {
                foreach ($data['stockQuantity'] as $key => $value) {
                    $warehouseCode = $value['warehouseCode'];
                    $stock = $value['normalUnitsInStock'] - $value['unitsAllocated'];
                    $final_data[$warehouseCode] = ['warehouseCode' => $warehouseCode, 'potentialStock' => $stock];
                }
            }
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
            $status['request'] = "";
            $status['response'] = "";

            return $status;
        } else {
            $err_msg = "Stock data not available";
            $status['Status'] = false;
            $status['error'] = "Err msg - " . $err_msg . " in function " . __METHOD__;
            $status['entity_id'] = '';
            $status['request'] = "";
            $status['response'] = "";

            return $status;
        }
    }

    public function getImages($img)
    {
        try {
//            if (!is_dir(UPLOAD_DIR)) {
//                $this->_io->mkdir(UPLOAD_DIR, 0775);
//            }
            $this->_io->checkAndCreateFolder(UPLOAD_DIR);
            $img = str_replace('data:image/png;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $data1 = base64_decode($img);
            $image_name = uniqid() . '.png';
            $file = rtrim(UPLOAD_DIR, '/') . '/' . $image_name;

            $media = $this->filesystem->getDirectoryWrite($this->_directoryList::MEDIA);
            $success = $media->writeFile($file, $data1);
            //$success = file_put_contents($file, $data1);
            //chmod($file, 0777);
            $success ? $file : false;

            return $file;
        } catch (Exception $e) {
            return false;
        }
        //return $image_name;
    }

    public function getcustomimages($fileurl, $imageName)
    {
        if (!is_dir(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR);
        }
        echo '<br/>' . $fileurl = $fileurl . '/' . $imageName;
        $fileurl = str_replace(" ", "%20", $fileurl);
        $fileurl = str_replace("(", "", $fileurl);
        $fileurl = str_replace(")", "", $fileurl);
        $image_name = uniqid() . '.jpg';
        $file = UPLOAD_DIR . $image_name;
        try {
            $contents = file_get_contents($fileurl);
        } catch (Exception $ex) {
            echo "File download error" . $ex->getMessage();

            return false;
        }
        if (strpos(file_get_contents($fileurl), 'Page Not Found') !== false) {
            echo "<br/>In if part";

            return false;
        } else {
            echo "<br/>In else";
            $success = file_put_contents($file, file_get_contents($fileurl));
            chmod($file, 0775);
            $success ? $file : false;

            return $file;
        }
    }

    public function createProduct($data, $visibility = 1)
    {
        if (!isset($data['results']['data']['attributes'])) {
            $update_data['Status'] = 'Fail';
            $update_data['Remarks'] = 'Attribute data not found';

            return $update_data;
        }
        $this->productvisibility = $visibility;
        $attribute_data = $data['results']['data']['attributes'];
        $itemName = $attribute_data['itemName'];
        $itemCode = $attribute_data['itemCode'];
        $product_found = $this->checkProductExistByItemCode($itemCode);

        if ($product_found) {
            print_r(['this->updateItemProduct($attribute_data, $product_found);']);
            $product_result = $this->updateItemProduct($attribute_data, $product_found);
        } else {
            print_r([' $this->insertItemNew($itemCode)']);
            $product_result = $this->insertItemNew($itemCode);
        }

        return $product_result;
    }

    public function updateItemProduct($data, $product_id)
    {
        $data1 = $data;
        $data = $data['item'];

        //$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $set_product = $this->productfactory->create()->setStoreId(0)->load($product_id);
        //$set_product->save();
        $set_product->setWebsiteIds([1, 2]);
//        $set_product->setStoreId(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
        $set_product->setStoreId(0);
        $webSiteCode = 'WEB-000001';
        /////////////////////////// Edited By Manisha to get value of isfeatured and published according to websitecode ////////////////////////
        $isFeatured = '';
        $isPublished = 1;
        $api_response_web_new = $this->getCurlData('inventory/weboption/' . $data['itemCode']);
        if ($api_response_web_new['api_error']) {
            foreach ($api_response_web_new['results']['data'] as $api_web_data) {
                if ($api_web_data['attributes']['webSiteCode'] == $webSiteCode) {
                    $data_web = $api_web_data['attributes'];
                    if (isset($data_web['isFeatured'])) {
                        $isFeatured = $data_web['isFeatured'];
                    }
                    if (isset($data_web['published'])) {
                        $isPublished = $data_web['published'];
                    }
                }
            }
        }
        $set_product->setIsfeatured($isFeatured);
        $set_product->setPublished($isPublished);

        if ($set_product->getData('interprise_item_type') != $data['itemType']) {
            $err_msg = "Item type not mactched in Magento:" . $set_product->getData('interprise_item_type');
            $err_msg .= " and in Interprise" . $data['itemType'];
            $update_data['Status'] = "Fail";
            $update_data['Remarks'] = "Err msg - " . $err_msg . " in function " . __METHOD__;

            return $update_data;
        }
        $product_sku = $data['itemName'];
        if (strlen($product_sku) > 64) {
            $errMsg = 'Sku length greater than 64 characters';

            $update_data['Status'] = 'Fail';
            $update_data['Remarks'] = "Err msg1 - " . $errMsg . " in function " . __METHOD__;

            return $update_data;
        }
        $isitemcode = $data['itemCode'];
        $product_name = $data1['description'][0]['itemDescription'];
        $product_description = (isset($data1['description'][0]['extendedDescription']) ? $data1['description'][0]['extendedDescription'] : $product_name);
        $seTitle = $product_name;
        $seKeywords = $product_description;
        $seDescription = $product_description;
        $webDescription = $product_description;

        $api_response_web = $this->getCurlData('inventory/weboption/' . $isitemcode . '/description');
        if ($api_response_web['api_error']) {
            foreach ($api_response_web['results']['data'] as $api_web_data) {
                if ($api_web_data['attributes']['webSiteCode'] == $webSiteCode) {
                    $data_web = $api_web_data['attributes'];
                }
            }
            if (isset($data_web)) {
                if (isset($data_web['seTitle'])) {
                    $seTitle = $data_web['seTitle'];
                }
                if (isset($data_web['seKeywords'])) {
                    $seKeywords = $data_web['seKeywords'];
                }
                if (isset($data_web['seDescription'])) {
                    $seDescription = $data_web['seDescription'];
                }
                if (isset($data_web['webDescription'])) {
                    $webDescription = $data_web['webDescription'];
                }
            }
        }

        $update_name = $this->getConfig('setup/general/update_name');
        $update_manufacturer = $this->getConfig('setup/general/update_manufacturer');
        $update_category = $this->getConfig('setup/general/update_category');

        if ($update_category) {

            if (isset($data1['category'])) {
                $categories = $data1['category'];
                $category_ids = [];
                foreach ($categories as $key => $value) {
                    $catExisting = $this->checkCategoryExistByIsCode($value["categoryCode"]);
                    if (!$catExisting) {
//                        file_put_contents("/var/www/html/var/interprise.log", json_encode(['notFoundCategory' => $value]) . "\n", FILE_APPEND);
                        print_r(['notFoundCategory' => $value]);
                    }
                    $category_ids = array_merge($category_ids, $catExisting);
                }
            }

            if (!isset($category_ids) || !$category_ids || $category_ids[0] == '') {
                $parent_id_default = $this->getConfig('setup/general/defaultcategory');
                $category_ids = [$parent_id_default];
            }

//            print_r(['UPDATE CAT' => $category_ids]);
//            $set_product->setCategoryIds($category_ids);
        }
        //$prices = $this->prices->getAllPrices($product_sku,$isitemcode,'GBP');
        $prices = $this->getAllPricesNew($data1, $product_sku, $isitemcode, 'GBP');
        $special_price_date_flag = false;
        $weight_flag = $data['isUseNetMassOrWeight'];
        if ($weight_flag) {
            $weight = 1;
        }

        if ($update_name == 1) {
            //$set_product->setName($product_name);
            $set_product->setName($data1['itemName']);
            $set_product->setMetaTitle($seTitle);
            $set_product->setMetaKeyword($seKeywords);
            $set_product->setMetaDescription($product_description);
//            $set_product->setDescription($webDescription);
//          $set_product->setShortDescription($seDescription);
        }

        if (isset($data1['unitOfMeasure'][0]['netWeightInKilograms']) && $data1['unitOfMeasure'][0]['netWeightInKilograms'] > 0) {
            $set_product->setWeight($data1['unitOfMeasure'][0]['netWeightInKilograms']);
        } else if (isset($data1['unitOfMeasure'][0]['weightInKilograms']) && $data1['unitOfMeasure'][0]['weightInKilograms'] > 0) {
            $set_product->setWeight($data1['unitOfMeasure'][0]['weightInKilograms']);
        } else {
            $set_product->setProductHasWeight(0);
            $set_product->setWeight(0);
        }

        if (isset($data1['unitOfMeasure'][0]['unitMeasureCode'])) {
            $set_product->setData('is_unitmeasurecode', $data1['unitOfMeasure'][0]['unitMeasureCode']);
        }

        //if ($update_manufacturer) {
        if (isset($data1['unitOfMeasure'][0]['upcCode'])) {
            $set_product->setUpccode($data1['unitOfMeasure'][0]['upcCode']);
        } else {
            $set_product->setUpccode('');
        }
        //}

        $mfgcode = "";
        if (isset($data1['item']['manufacturerCode'])) {
            $set_product->setManufacturercode($data1['item']['manufacturerCode']);
            $mfgcode = $data1['item']['manufacturerCode'];
        }
        if ($update_manufacturer) {
            if (isset($data1['item']['manufacturerCode'])) {
                $set_product->setManufacturercode($data1['item']['manufacturerCode']);
            } else {
                $set_product->setManufacturercode('');
            }
        }
        $set_product->setData('ext_description', $product_description);

        $set_product->setTaxClassId(2);

        if ($data1['item']['salesTaxOption'] == 'Customer') {
            $set_product->setData('is_vatexempt', 1);
        } else {
            $set_product->setData('is_vatexempt', 0);
        }

        $set_product->setVisibility($this->productvisibility);

        $set_product->setPrice($prices['price']);

        $set_product->setData('is_retailprice', $prices['retail_price']);

        $is_specialPrice = ['special_price' => $prices['special_price'], 'date_from' => $prices['dateFrom'], 'date_to' => $prices['dateTo']];
        $set_product->setData('is_specialprice', json_encode($is_specialPrice));

        $set_product->setData('is_wholesaleprice', $prices['wholesale_price']);
        $set_product->setMsrpEnabled(1);
        $set_product->setMsrpDisplayActualPriceType(1);
        // display actual price
        // (1 - on gesture, 2 - in cart, 3 - before order confirmation, 4 - use config)
        $set_product->setMsrp($prices['msrp']);
        $set_product->setInterpriseStatus($data['status']);
        echo '<br/>status ' . $data['status'];
        if ($data['status'] == "D") {
            $set_product->setStatus(2);
            $set_product->setSku($product_sku);
        } else {
            if ($isPublished == '1') {
                $set_product->setStatus(1);
                $set_product->setSku($product_sku);
            } else {
                $set_product->setStatus(2);
                $set_product->setSku($product_sku);
            }
        }
        $set_product->setInterpriseStatus($data['status']);

        $custom_fields = ['webid_c'];

        $custom_fields_dropdown = [];

        if (isset($data1['item']['customFields']) && count($data1['item']['customFields']) > 0) {
            foreach ($data1['item']['customFields'] as $key => $item) {
                if (in_array(strtolower($item['field']), $custom_fields)) {
                    $set_product->setData(strtolower($item['field']), $item['value']);
                } else if (in_array(strtolower($item['field']), $custom_fields_dropdown)) {
                    //$eavConfig = $objectManager->get('\Magento\Eav\Model\Config');
                    $attributeCode = strtolower(trim($item['field']));
                    $attributeValue = trim($item['value']);
                    $attribute = $this->eavConfig->getAttribute('catalog_product', $attributeCode);
                    $optionID = $attribute->getSource()->getOptionId($attributeValue);
                    if (isset($optionID) && $optionID != '') {
                        //echo "<br/>In If";
                        $set_product->setData($attributeCode, $optionID);
                    } else {
                        //echo "<br/>In Else";
                        $attributeID = $attribute->getId();
                        //$attributeOptionManagement =  $objectManager->get('\Magento\Eav\Api\AttributeOptionManagementInterface');
                        //$optionLabelFactory = $objectManager->get('\Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory');
                        //$optionFactory = $objectManager->get('\Magento\Eav\Api\Data\AttributeOptionInterfaceFactory');

                        $attributeOptionManagement = $this->attributeOptionManagementInterface;
                        $optionLabelFactory = $this->attributeOptionLabelFactory;
                        $optionFactory = $this->attributeOptionFactory;

                        $optionLabel = $optionLabelFactory->create();
                        $optionLabel->setStoreId(0);
                        $optionLabel->setLabel($attributeValue);

//                                            $optionLabel1 = $optionLabelFactory->create();
                        //                                           $optionLabel1->setStoreId(1);
                        //                                          $optionLabel1->setLabel($attributeValue);

                        $option = $optionFactory->create();
                        $option->setLabel($attributeValue);
                        $option->setStoreLabels([$optionLabel]);
                        $option->setSortOrder(0);
                        $option->setIsDefault(false);

                        $attributeOptionManagement->add(
                            'catalog_product',
                            $attributeCode,
                            $option
                        );
                        $attribute = $this->eavConfig->getAttribute('catalog_product', $attributeCode);
                        $optionID = $attribute->getSource()->getOptionId($attributeValue);
                        $set_product->setData($attributeCode, $optionID);
                    }
                }
            }
        }

        $custom_fields = [];
        $set_product->save();

        $counter = $data['counter'];
        $url = preg_replace('#[^0-9a-z]+#i', '-', 'p-' . $counter . '-' . $product_name);
        $url = strtolower($url);

        $seturl = $set_product->getUrlKey();

        if ($seturl <> $url) {
            $set_product->setUrlKey($url);
        }
        try {
            echo '<br/>' . $get_product_id = $set_product->getId();
            $linkDataAll = [];
            //$set_product->save();
            /*--------------- Product to insert product accessory data ------------------------ */

//            $accessoryData = array();
//            if(isset($data1['accessoryItem']))
//                $accessoryData = $data1['accessoryItem'];
//
//            $this->resource->getConnection()->delete('lamp_accessory_mapping', "product_id = '".$isitemcode."'");
//
//            $substituteData = array();
//            $linkDataAll = [];
//            $i =0;
//            $this->resource->getConnection()->delete('catalog_product_link', "product_id = '".$get_product_id."' AND link_type_id IN(1,4)");
//            if(!empty($accessoryData)){
//                foreach($accessoryData as $accessory){
//                    $accessoryID = $accessory['accessoryCode'];
//                    $insData[] = ['product_id' =>$isitemcode, 'accessory_id' => $accessoryID];
//
//                    $linkedProduct = $this->productfactory->create()->loadByAttribute("interprise_item_code",$accessoryID);
//                    if($linkedProduct) {
////                        echo '<br/>Product Found !!';
////                        echo '<br/>'.$linkedProduct->getId();
////                        echo '<br/>'.$linkedProduct->getSku();
//                        $linkData = $this->productLinks->create()
//                            ->setSku($set_product->getSku())
//                            ->setLinkedProductSku($linkedProduct->getSku())
//                            ->setLinkType("upsell");
//                        $linkDataAll[] = $linkData;
//
//                        //$relatedProductsArranged[$linkedProduct->getId()] = array('position' => $i++);
//                    }
//                }
//                $this->resource->getConnection()->insertMultiple('lamp_accessory_mapping', $insData);
//            }
            // die;

            /*------------------- End Code for Product accessory data ------------------------ */

            /*--------------------------------------- Code to update upsell product ---------------------------------*/

            if (isset($data1['substituteItem'])) {
                foreach ($data1['substituteItem'] as $substituteNote) {
                    $substituteCode = $substituteNote['substituteCode'];
                    $linkedProduct = $this->productfactory->create()->setStoreId(0)->loadByAttribute("interprise_item_code", $substituteCode);
                    if ($linkedProduct) {
//                        echo '<br/>Product Found!!';
//                        echo '<br/>'.$linkedProduct->getId();
//                        echo '<br/>'.$linkedProduct->getSku();
                        $linkData = $this->productLinks->create()
                            ->setSku($set_product->getSku())
                            ->setLinkedProductSku($linkedProduct->getSku())
                            ->setLinkType("related");
                        $linkDataAll[] = $linkData;
                        //$relatedProductsArranged[$linkedProduct->getId()] = array('position' => $i++);
                    }
                }
            }

            if ($linkDataAll) {
                $set_product->setProductLinks($linkDataAll);
            }
            try {
                $set_product->save();

                $this->updateWhosalePrice($set_product);
            } catch (Exception $e) {
                $err_msg = $e->getMessage();
                $update_data['Status'] = "Fail";
                $update_data['Remarks'] = "Error - " . $err_msg . " in function " . __METHOD__;

                return $update_data;
            }

            /*--------------------------------------End Code to update upsell product ---------------------------------*/

            $update_data['Status'] = "Success";
            $update_data['Remarks'] = "Item id " . $get_product_id . " updated";
            $update_data['Entity'] = $get_product_id;

            return $update_data;
        } catch (Exception $ex) {
            $err_msg = $ex->getMessage();
            $update_data['Status'] = "Fail";
            $update_data['Remarks'] = "Error - " . $err_msg . " in function " . __METHOD__;

            return $update_data;
        }
    }

    public function getItemDescription($item)
    {
        $description = $this->getCurlData('inventory/' . $item . '/description');
        if ($description['api_error']) {
            $result = $description['results']['data'][0]['attributes'];
            $data['itemDescription'] = $result['itemDescription'];
            if (isset($result['extendedDescription'])) {
                $data['extendedDescription'] = $result['extendedDescription'];
            } else {
                $data['extendedDescription'] = $result['itemDescription'];
            }

            return $data;
        }
        $data['itemDescription'] = $item;
        $data['extendedDescription'] = $item;

        return $data;
    }

    /*-------------------------------- Code to update inventory custom Data Start ------------------------------------------*/

    public function getcustomimages_old($fileurl, $image_name)
    {
//        if (!is_dir(UPLOAD_DIR)) {
//            $this->_io->mkdir(UPLOAD_DIR, 0775);
//        }
        $this->_io->checkAndCreateFolder(UPLOAD_DIR);
        echo '<br/>' . $fileurl = str_replace(" ", "%20", $fileurl);
        echo '<br/>' . $image_name_new = uniqid() . '.jpg';
        $file = UPLOAD_DIR . $image_name;
        try {
            $media = $this->filesystem->getDirectoryWrite($this->_directoryList::MEDIA);
            echo '<br/>' . $result = $this->_io->read($fileurl, $image_name);
        } catch (Exception $ex) {
            echo "File download error" . $ex->getMessage();

            return false;
        }
        if (strpos($result, 'Page Not Found') !== false) {
            return false;
        } else {
            $success = $media->writeFile($image_name_new, $result);
            //chmod($file, 0775);
            $success ? $file : false;

            return $file;
        }
    }

    /*-------------------------------End Code to update inventory custom Data Start ------------------------------------------*/

    public function inventoryCustomUpdateSingle($data, $visibility = 4)
    {
        ini_set('display_errors', '1');
        $this->productvisibility = $visibility;
        $dataId = $data['DataId'];

        $product_found = $this->checkProductExistByItemCode($dataId);
        if (!$product_found) {
            //$update_data = $this->insertItemNew($dataId, $visibility);
        } else {
            try {
                /*---------------------------- Code to update accessory --------------------------------------*/
//                $api_responsc = $this->getCurlData('inventory/'.$dataId.'/accessory');
//                $update_data['ActivityTime'] = $this->getCurrentTime();
//                $update_data['Request'] = $api_responsc['request'];
//                $update_data['Response'] = json_encode($api_responsc['results']);
//                if ($api_responsc['api_error']) {
//                    $update_data['ActivityTime'] = $this->getCurrentTime();
//                    $update_data['Request'] = $api_responsc['request'];
//                    $update_data['Response'] = json_encode($api_responsc['results']['data']);
//                    if ($api_responsc['api_error'] && isset($api_responsc['results']['data'])) {
//
//                        $dataAccessory = $api_responsc['results']['data'];
//                        $isitemcode = $dataId;
//
//                        $this->resource->getConnection()->delete('lamp_accessory_mapping', "product_id = '".$isitemcode."'");
//
//                        if(count($dataAccessory) > 0){
//
//                            $insData = [];
//                            foreach($dataAccessory as $accessory){
//
//                                $accessoryID = $accessory['attributes']['accessoryCode'];
//                                $insData[] = ['product_id' =>$isitemcode, 'accessory_id' => $accessoryID];
//                            }
//
//                            $this->resource->getConnection()->insertMultiple('lamp_accessory_mapping', $insData);
//                        }
//                        $update_data['Status'] = 'Success';
//                        $update_data['Remarks'] = 'Acessory updated for '. $dataId .'.' ;
//                    } else {
//                        $update_data['Status'] = 'Fail';
//                        $update_data['Remarks'] = $api_responsc['results'];
//                    }
//                } else {
//                    $update_data['Status'] = 'Fail';
//                    $update_data['Remarks'] = 'No response from inventory/' . $dataId .'/accessory';
//                }
//                /*--------------------------End Code to update accessory --------------------------------------*/
//                /*-------------------------- Code to update ext_description --------------------------------------*/
//                $extendedDescription = '';
//                $description = $this->getCurlData('inventory/' . $dataId . '/description');
//                if ($description['api_error']) {
//                    $result = $description['results']['data'][0]['attributes'];
//                    if (isset($result['extendedDescription'])) {
//                        $extendedDescription = $result['extendedDescription'];
//                    }
//                    $product = $this->productfactory->create()->load($product_found);
//                    $product->setData('ext_description', $extendedDescription);
//                    $product->save();

                $update_data['Status'] = 'Success';
                $update_data['Remarks'] = $update_data['Remarks'] . ' ext_description attribute updated for ' . $dataId . '.';
//                } else{
//                    $update_data['Status'] = 'Fail';
//                    $update_data['Remarks'] = $update_data['Remarks']. 'No response from inventory/' . $dataId . '/description';
//                }
//

                /*------------------------End Code to update ext_description --------------------------------------*/
            } catch (Exception $ex) {
                $err_msg = $ex->getMessage();
                $update_data['Status'] = 'Fail';
                $update_data['Remarks'] = 'In method ' . __METHOD__ . ' ' . $err_msg;
            }

            return $update_data;
        }

        return $update_data;
    }

    public function getProductIdByItemCode($dataId)
    {
        //$query = "SELECT entity_id FROM catalog_product_entity where entity_id in (select entity_id from
        // catalog_product_entity_varchar WHERE attribute_id=167 and VALUE LIKE '$dataId') limit 1";
        //echo $dataId;
        $productcollection = $this->productCollectionFactory->create();
        $productcollection->addAttributeToSelect('*');
        $productcollection->addAttributeToFilter('interprise_item_code', $dataId);
        $productcollection->setPageSize(1)->setCurPage(1);

        if ($productcollection->getSize() > 0) {
            $result = $productcollection->getData();

            return $result[0]['entity_id'];
        } else {
            return false;
        }
    }

    /*************************************** Function for different changelogs *********************************************/

    public function inventoryItemDescriptionSingle($data)
    {
        $dataId = $data['DataId'];
        if ($this->checkDepartmentFromInterprise($dataId)) {
            $product_found = $this->checkProductExistByItemCode($dataId);

            if (!$product_found) {
                $update_data = $this->insertItemNew($dataId);
            } else {
                try {
                    $update_name = 1 ;//$this->getConfig('setup/general/update_name');
                    if ($update_name == 1) {
                        $description = $this->getCurlData('inventory/' . $dataId . '/description');

                        $update_data['ActivityTime'] = $this->getCurrentTime();
                        $update_data['Request'] = $description['request'];
                        $update_data['Response'] = json_encode($description['results']);
                        if ($description['api_error']) {
                            $result = $description['results']['data'][0]['attributes'];
                            $itemDescription = $result['itemDescription'];
                            if (isset($result['extendedDescription'])) {
                                $extendedDescription = $result['extendedDescription'];
                            } else {
                                $extendedDescription = $result['itemDescription'];
                            }

                            $productRepository = ObjectManager::getInstance()->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
                            $set_product = $productRepository->getById($product_found, true, 0, true);

                            $product_name = $itemDescription;
                            if (strtolower($set_product->getData('interprise_item_type')) == 'matrix item') {
                                $product_name = $set_product->getSku();
                            }

                            $seTitle = $itemDescription;
                            $seKeywords = $itemDescription;
                            $seDescription = $extendedDescription;
                            $webDescription = $extendedDescription;

                            $set_product->setName($product_name);
                            $set_product->setMetaTitle($seTitle);
                            $set_product->setMetaKeyword($seKeywords);
                            $set_product->setMetaDescription($extendedDescription);
//                            $set_product->setDescription($webDescription);
//                            $set_product->setShortDescription($seDescription);
                            $set_product->save();
                            $update_data['Status'] = 'Success';
                            $update_data['Remarks'] = ' Description updated for ' . $dataId . '.';
                        } else {
                            $update_data['Status'] = 'Fail';
                            $update_data['Remarks'] = 'No response from inventory/' . $dataId . '/description';
                        }
                    }
                } catch (Exception $ex) {
                    $err_msg = $ex->getMessage();
                    $update_data['Status'] = 'Fail';
                    $update_data['Remarks'] = 'In method ' . __METHOD__ . ' ' . $err_msg;
                }
            }
        } else {
            $update_data['Status'] = 'Department not matched';
            $update_data['Response'] = '';
            $update_data['ActivityTime'] = $this->getCurrentTime();
            $update_data['Remarks'] = json_encode('Department does not match');
        }

        return $update_data;
    }

    public function inventoryItemWebOptionSingle($data)
    {
        echo "<br> here also <br>";
        $dataId = $data['DataId'];
        if ($this->checkDepartmentFromInterprise($dataId)) {
            $product_found = $this->checkProductExistByItemCode($dataId);

            if (!$product_found) {
                $update_data = $this->insertItemNew($dataId);
            } else {
                try {
                    $isFeatured = '';
                    $isPublished = 1;
                    $webSiteCode = 'WEB-000001';
                    $api_response_web_new = $this->getCurlData('inventory/weboption/' . $dataId);
                    $update_data['ActivityTime'] = $this->getCurrentTime();
                    $update_data['Request'] = $api_response_web_new['request'];
                    $update_data['Response'] = json_encode($api_response_web_new['results']);
                    if ($api_response_web_new['api_error']) {
                        foreach ($api_response_web_new['results']['data'] as $api_web_data) {
                            if ($api_web_data['attributes']['webSiteCode'] == $webSiteCode) {
                                $data_web = $api_web_data['attributes'];
                                if (isset($data_web['isFeatured'])) {
                                    $isFeatured = $data_web['isFeatured'];
                                }
                                if (isset($data_web['published'])) {
                                    $isPublished = $data_web['published'];
                                }
                            }
                        }
                        $set_product = $this->productfactory->create()->setStoreId(0)->load($product_found);

                        $set_product->setWebsiteIds([1, 2]);
                        //                    $set_product->setStoreId(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
                        $set_product->setStoreId(0);

                        $set_product->setIsfeatured($isFeatured);
                        $set_product->setPublished($isPublished);

                        $set_product->save();
                        $update_data['Status'] = 'Success';
                        $update_data['Remarks'] = ' Published and Featured attributes updated for ' . $dataId . ' according to Website ID.';
                    } else {
                        $update_data['Status'] = 'Fail';
                        $update_data['Remarks'] = 'No response from inventory/weboption/' . $dataId;
                    }
                } catch (Exception $ex) {
                    $err_msg = $ex->getMessage();
                    $update_data['Status'] = 'Fail';
                    $update_data['Remarks'] = 'In method ' . __METHOD__ . ' ' . $err_msg;
                }
            }
        } else {
            $update_data['Status'] = 'Department not matched';
            $update_data['Response'] = '';
            $update_data['ActivityTime'] = $this->getCurrentTime();
            $update_data['Remarks'] = json_encode('Department does not match');
        }

        return $update_data;
    }

    public function inventoryUnitMeasureSingle($data)
    {
        ini_set('display_errors', '1');
        $dataId = $data['DataId'];
        if ($this->checkDepartmentFromInterprise($dataId)) {
            $product_found = $this->checkProductExistByItemCode($dataId);

            if (!$product_found) {
                $update_data = $this->insertItemNew($dataId);
            } else {
                try {
                    $api_responsc = $this->getCurlData('inventory/' . $dataId . '/unitofmeasure');
                    if ($api_responsc['results']['data'] && $api_responsc['api_error']) {
                        $update_data['ActivityTime'] = $this->getCurrentTime();
                        $update_data['Request'] = $api_responsc['request'];
                        $update_data['Response'] = json_encode($api_responsc['results']['data']);
                        if ($api_responsc['api_error'] && isset($api_responsc['results']['data'])) {
                            if (!isset($api_responsc['results']['data'][0]['attributes'])) {
                                $update_data['Status'] = 'Fail';
                                $update_data['Remarks'] = 'Attribute data not found';

                                return $update_data;
                            }
                            $data1 = $api_responsc['results']['data'][0]['attributes'];

                            $update_manufacturer = $this->getConfig('setup/general/update_manufacturer');
                            $set_product = $this->productfactory->create()->setStoreId(0)->load($product_found);

                            if (isset($data1['netWeightInKilograms']) && $data1['netWeightInKilograms'] > 0) {
                                $set_product->setWeight($data1['netWeightInKilograms']);
                            } else if (isset($data1['weightInKilograms']) && $data1['weightInKilograms'] > 0) {
                                $set_product->setWeight($data1['weightInKilograms']);
                            } else {
                                $set_product->setProductHasWeight(0);
                                $set_product->setWeight(0);
                            }

                            if (isset($data1['unitMeasureCode'])) {
                                $set_product->setData('is_unitmeasurecode', $data1['unitMeasureCode']);
                            }

                            if ($update_manufacturer) {
                                if (isset($data1['upcCode'])) {
                                    $set_product->setUpccode($data1['upcCode']);
                                } else {
                                    $set_product->setUpccode('');
                                }
                            }
                            $set_product->save();
                            $update_data['Status'] = 'Success';
                            $update_data['Remarks'] = 'UnitOfMeasure, upcCode and weight updated for ' . $dataId;
                        } else {
                            $update_data['Status'] = 'Fail';
                            $update_data['Remarks'] = 'No response from inventory/' . $dataId . '/unitofmeasure';
                        }
                    } else {
                        $update_data['Status'] = 'Fail';
                        $update_data['Remarks'] = 'No response from inventory/' . $dataId . '/unitofmeasure';
                    }
                } catch (Exception $ex) {
                    $err_msg = $ex->getMessage();
                    $update_data['Status'] = 'Fail';
                    $update_data['Remarks'] = 'In method ' . __METHOD__ . ' ' . $err_msg;
                }
            }
        } else {
            $update_data['Status'] = 'Department not matched';
            $update_data['Response'] = '';
            $update_data['ActivityTime'] = $this->getCurrentTime();
            $update_data['Remarks'] = json_encode('Department does not match');
        }

        return $update_data;
    }

    public function itemSpecialPriceSingle($data)
    {
        $dataId = $data['DataId'];
        if ($this->checkDepartmentFromInterprise($dataId)) {
            $product_found = $this->checkProductExistByItemCode($dataId);

            if (!$product_found) {
                $update_data = $this->insertItemNew($dataId);
            } else {
                try {
                    $set_product = $this->productfactory->create()->setStoreId(0)->load($product_found);
                    $action = strtolower(trim($data['ActionType']));
                    $update_data['ActivityTime'] = $this->getCurrentTime();
                    if ($action == 'delete') {
                        $set_product->setData('is_specialprice', '');
                        $set_product->save();

                        $update_data['Request'] = "Special Price deleted";
                        $update_data['Response'] = 'Special Price deleted';
                        $update_data['Status'] = 'Success';
                        $update_data['Remarks'] = 'Special Price deleted for ' . $dataId;
                    } else {
                        $special_prices = $this->prices->getSpecialPriceItem($dataId, 'GBP');
                        echo '<pre>';
                        print_r($special_prices);
                        echo '</pre>';
                        if (isset($special_prices['special_price'])) {
                            $special_price = $special_prices['special_price'];
                        } else {
                            $special_price = '';
                        }

                        if (isset($special_prices['dateFrom']) && $special_prices['dateFrom'] != ''
                            &&
                            isset($special_prices['dateTo']) && $special_prices['dateTo'] != ''
                        ) {
                            echo '<br/>' . $dateFrom = $special_prices['dateFrom'];
                            echo '<br/>' . $dateTo = $special_prices['dateTo'];
                        } else {
                            $dateFrom = '';
                            $dateTo = '';
                        }
                        $is_specialPrice = ['special_price' => $special_price, 'date_from' => $dateFrom, 'date_to' => $dateTo];
                        $jsonPrices = json_encode($is_specialPrice);
                        $set_product->setData('is_specialprice', $jsonPrices);

                        $set_product->save();

                        $update_data['Request'] = "Special Price updated";
                        $update_data['Response'] = 'Special Price updated';
                        $update_data['Status'] = 'Success';
                        $update_data['Remarks'] = 'Special Price updated for ' . $dataId;
                    }

                    $this->updateWhosalePrice($set_product);
                } catch (Exception $ex) {
                    $update_data['Request'] = "Special Price update failed";
                    $update_data['Response'] = 'Special Price update failed';
                    $update_data['Status'] = 'Fail';
                    $update_data['Remarks'] = $ex->message();
                }
            }
        } else {
            $update_data['Status'] = 'Department not matched';
            $update_data['Response'] = '';
            $update_data['ActivityTime'] = $this->getCurrentTime();
            $update_data['Remarks'] = json_encode('Department does not match');
        }

        return $update_data;
    }

    public function inventoryCategorySingle($data)
    {
        return;
        ini_set('display_errors', '1');
        $dataId = $data['DataId'];
        $product_found = $this->checkProductExistByItemCode($dataId);

        if (!$product_found) {
            //$update_data = $this->insertItemNew($dataId);
        } else {
            try {
                $set_product = $this->productfactory->create()->setStoreId(0)->load($product_found);
                $action = strtolower(trim($data['ActionType']));
                $update_data['ActivityTime'] = $this->getCurrentTime();
                $category_ids = [];
                //if($action=='delete'){
//                    $parent_id_default = $this->getConfig('setup/general/defaultcategory');
//                    //$currentCategories  = $set_product->getCategoryIds();
//                    //$set_product->setCategoryIds(array($parent_id_default));
//
//                    //$set_product->save();
//                    $currentCategories  = $set_product->getCategoryIds();
//                    echo '<pre>';
//                    print_r($currentCategories);
//                    echo '</pre>';
//
//                    if($currentCategories){
//                        $this->unassignProduct($set_product->getId(), $currentCategories);
//                    }
//
//
////                    foreach ($currentCategories as $categoryId) {
////                        echo '<br/>'.$categoryId;
////                            $this->categoryLinkRepository->deleteByIds($categoryId, $set_product->getSku());
////                    }
//
//
//                    $update_data['Status'] = 'Success';
//                    $update_data['Remarks'] = 'Categories updated for '. $dataId .'.' ;
//                    $update_data['Response'] = json_encode($data);
//                    $update_data['Request'] = '';

                //} else{
                $api_responsc = $this->getCurlData('inventory/' . $dataId . '/category');
                $update_data['ActivityTime'] = $this->getCurrentTime();
                $update_data['Request'] = $api_responsc['request'];
                $update_data['Response'] = json_encode($api_responsc['results']);
                echo '<pre>';
                print_r($api_responsc);
                echo '</pre>';

                if ($api_responsc['api_error']) {
                    $update_data['ActivityTime'] = $this->getCurrentTime();
                    $update_data['Request'] = $api_responsc['request'];
                    $update_data['Response'] = json_encode($api_responsc['results']['data']);
                    if ($api_responsc['api_error'] && isset($api_responsc['results']['data'])) {
                        $categories = $api_responsc['results']['data'];
                        $isitemcode = $dataId;
//                            echo '<pre>';
//                            print_r($categories);
//                            echo '</pre>';
                        foreach ($categories as $key => $value) {
                            $categoryValue = $value['attributes']['categoryCode'];
                            $catId = $this->checkCategoryExistByIsCode($categoryValue);
                            if ($catId) {
                                $category_ids[] = $catId;
                            } else {
                                $update_data['Status'] = 'Fail';
                                $update_data['Remarks'] = 'Category "' . $categoryValue . '" not found in Magento admin.';

                                return $update_data;
                            }
                        }
                        $currentCategories = $set_product->getCategoryIds();
                        echo '<pre>$currentCategories';
                        print_r($currentCategories);
                        echo '</pre>';
                        echo '<pre>$category_ids';
                        print_r($category_ids);
                        echo '</pre>';
                        $deleteCategories = array_diff($currentCategories, $category_ids);
                        echo '<pre>$deleteCategories';
                        print_r($deleteCategories);
                        echo '</pre>';

                        $assignCategories = array_diff($category_ids, $currentCategories);
                        echo '<pre>$assignCategories';
                        print_r($assignCategories);
                        echo '</pre>';
//                        $set_product->setCategoryIds($assignCategories);
                        $set_product->save();
//                        foreach ($category_ids as $category) {
//                            if (!in_array($category, $deleteCategories)) {
//                                $this->assignProduct($set_product->getId(), $category);
//                            }
//                        }
//                        if (!empty($deleteCategories)) {
//                            $this->unassignProduct($set_product->getId(), $deleteCategories);
//                        }
                        //$newCategoryIds = array();
                        //$this->_categoryLinkManagementInterface->assignProductToCategories($set_product->getSku(), $category_ids);
                        $update_data['Status'] = 'Success';
                        $update_data['Remarks'] = 'Categories updated for ' . $dataId . '.';
                    } else {
                        $update_data['Status'] = 'Fail';
                        $update_data['Remarks'] = $api_responsc['results'];
                    }
                } else {
                    $currentCategories = $set_product->getCategoryIds();
                    if ($currentCategories) {
                        $this->unassignProduct($set_product->getId(), $currentCategories);
                    }
                    $update_data['Status'] = 'Success';
                    $update_data['Remarks'] = 'Categories updated for ' . $dataId . '.';
                    $update_data['Response'] = json_encode($data);
                    $update_data['Request'] = '';
                }
                //}

            } catch (Exception $ex) {
                $update_data['Request'] = "Inventory Category assignment failed";
                $update_data['Response'] = 'Inventory Category assignment failed';
                $update_data['Status'] = 'Fail';
                $update_data['Remarks'] = $ex->message();
            }
        }

        return $update_data;
    }

    public function assignProduct($entityID, $categoryID)
    {
        $query = "select * from catalog_category_product where category_id=$categoryID and product_id=$entityID";
        $result = $this->resource->getConnection()->fetchRow($query);
        if (empty($result)) {
            echo "Inside Condition";
            $insData = [];
            $insData['category_id'] = $categoryID;
            $insData['product_id'] = $entityID;
            $insData['position'] = 0;
            $this->resource->getConnection()->insert('catalog_category_product', $insData);
        }
    }

    public function unassignProduct($entityID, $categoryID)
    {
        $query = "DELETE from catalog_category_product where product_id='" . $entityID . "' and category_id IN(" . implode(",", $categoryID) . ")";
        $this->resource->getConnection()->query($query);
    }

    public function categoryWebOptionSingle($data)
    {
        ini_set('display_errors', '1');
        $dataId = $data['DataId'];
        $action = strtolower(trim($data['ActionType']));
        if ($action == 'delete') {
            $response = $this->deleteCategory($data);
        } else {
            $response = $this->createCategory($data);
        }
        echo '<pre>';
        print_r($response);

        return $response;
    }

    public function deleteCategory($data)
    {
        echo '<br/>$catcode' . $catcode = $data['DataId'];
        $categoryId = $this->checkCategoryExistByIsCode($catcode);
        if ($categoryId) {
            $this->updateCategoryStatus($categoryId, 0);
            $update_data['Status'] = 'Success';
            $update_data['Remarks'] = 'Category disabled from Magento';
            $update_data['Request'] = '';
            $update_data['Response'] = 'Category disabled from Magento';
            $update_data['ActivityTime'] = $this->getCurrentTime();
        } else {
            $update_data['Status'] = 'Fail';
            $update_data['Remarks'] = 'Category not found in Magento';
            $update_data['Request'] = '';
            $update_data['Response'] = 'Category not found in Magento';
            $update_data['ActivityTime'] = $this->getCurrentTime();
        }

        return $update_data;
    }

    public function updateCategoryStatus($categoryID, $status = 0)
    {
        $query = "UPDATE catalog_category_entity_int set value='" . $status . "' where attribute_id=46 and entity_id='" . $categoryID . "'";
        $this->resource->getConnection()->query($query);
    }

    public function createCategory($data)
    {
        $dataId = $data['DataId'];
        $default_cat = $this->getConfig('setup/general/defaultcategory');
        $catcode = $dataId;
        if (!$this->checkCategoryExistByIsCode($dataId)) {
            //echo '<br/>$catcode'.$parent=$cat["parentCategory"];
            $api_responsc = $this->getCurlData('system/category?categoryCode=' . $catcode);
            echo '<pre>';
            print_r($api_responsc);
            echo '</pre>';
            $update_data['ActivityTime'] = $this->getCurrentTime();
            if ($api_responsc['api_error']) {
                $update_data['Request'] = $api_responsc['request'];
                $update_data['Response'] = json_encode($api_responsc['results']['data']);

                if (!isset($api_responsc['results']['data']['attributes'])) {
                    $update_data['Status'] = 'Fail';
                    $update_data['Remarks'] = 'Attribute data not found';

                    return $update_data;
                }

                $cat = $api_responsc['results']['data']['attributes'];
//                    echo '<br/>Manisha';
//                    echo '<pre>$cat';
//                    print_r($cat);
//                    echo '</pre>';
                if (!empty($cat)) {
                    echo '<br/>$parent' . $parent = $cat["parentCategory"];
                    echo '<br/>$parent_id' . $parent_id = $this->checkCategoryExistByIsCode($parent);
                    $api_responsc_desc = $this->getCurlData('system/category/weboption/description?categoryCode=' . $catcode);
                    if ($api_responsc_desc['results']['data'] && $api_responsc_desc['api_error']) {
                        $categoryDescription = '';
                        if (!isset($api_responsc_desc['results']['data'])) {
                            $categoryDescription = '';
                        } else {
                            $webSiteCode = 'WEB-000001';
                            foreach ($api_responsc_desc['results']['data'] as $api_catdesc_data) {
                                if ($api_catdesc_data['attributes']['webSiteCode'] == $webSiteCode && isset($api_catdesc_data['attributes']['webDescription'])) {
                                    $categoryDescription = $api_catdesc_data['attributes']['webDescription'];
                                }
                            }
                        }
                    }
                    echo '<br/>$categoryDescription ' . $categoryDescription;
                    $categoryData = [];
                    $newCategoryID = '';
                    if ($parent == "DEFAULT" || $parent == $catcode) {
                        $newCategoryID = $this->createCategories2($catcode, $categoryDescription, $default_cat, $cat['isActive']);
                    } else if ($parent_id > 0) {
                        $newCategoryID = $this->createCategories2($catcode, $categoryDescription, $parent_id, $cat['isActive']);
                    }
                    if ($newCategoryID) {
                        $update_data['Request'] = $api_responsc['request'];
                        $update_data['Response'] = json_encode($api_responsc['results']['data']);
                        $update_data['Status'] = 'Success';
                        $update_data['Remarks'] = 'Category ' . $dataId . ' successfully created. Category ID ' . $newCategoryID;
                    } else {
                        $update_data['Request'] = $api_responsc['request'];
                        $update_data['Response'] = json_encode($api_responsc['results']['data']);
                        $update_data['Status'] = 'Fail';
                        $update_data['Remarks'] = 'Something went wrong!!';
                    }
                } else {
                    $update_data['Request'] = $api_responsc['request'];
                    $update_data['Response'] = json_encode($api_responsc['results']);
                    $update_data['Status'] = 'Fail';
                    $update_data['Remarks'] = 'Category Not Found!!';
                }
            } else {
                $update_data['Request'] = $api_responsc['request'];
                $update_data['Response'] = json_encode($api_responsc['results']);
                $update_data['Status'] = 'Fail';
                $update_data['Remarks'] = 'Data Not Found!!';
            }
        } else {
            $update_data['Request'] = $api_responsc['request'];
            $update_data['Response'] = 'Category already exist in magento!!';
            $update_data['Status'] = 'Already Exist!!';
            $update_data['Remarks'] = 'Category already exist in magento!!';
//            $categoryId = $this->checkCategoryExistByIsCode($dataId);
//            $api_responsc = $this->getCurlData('system/category?categoryCode='.$catcode);
//            $update_data['ActivityTime'] = $this->getCurrentTime();
//            if ($api_responsc['results']['data'] && $api_responsc['api_error']) {
//                echo "Inside Else";
//
//                $update_data['Request'] = $api_responsc['request'];
//                $update_data['Response'] = json_encode($api_responsc['results']['data']);
//
//
//                if (!isset($api_responsc['results']['data']['attributes'])) {
//                    $update_data['Status'] = 'Fail';
//                    $update_data['Remarks'] = 'Attribute data not found';
//                    return $update_data;
//                }
//
//                $cat = $api_responsc['results']['data']['attributes'];
//
//                echo '<pre>$cat';
//                print_r($cat);
//                echo '</pre>';
//                if(!empty($cat)){
//                    $is_active = $cat['isActive'];
//                    $this->updateCategoryStatus($categoryId, $is_active);
//                }
//
//                $update_data['Request'] = $api_responsc['request'];
//                $update_data['Response'] = json_encode($api_responsc['results']['data']);
//                $update_data['Status'] = 'Success';
//                $update_data['Remarks'] = 'Category '. $dataId .' successfully updated.' ;
//
//            } else{
//                $update_data['Request'] = $api_responsc['request'];
//                $update_data['Response'] = json_encode($api_responsc['results']['data']['attributes']);
//                $update_data['Status'] = 'Fail';
//                $update_data['Remarks'] = 'Data Not Found!!';
//            }
        }

        return $update_data;
    }

    public function createCategories2($categorycode, $categoryDescription, $parent_id, $isactive)
    {
        ini_set('display_errors', '1');
        $query = "select path from catalog_category_entity where entity_id='" . $parent_id . "'";
        $result = $this->resource->getConnection()->fetchRow($query);
        echo '<pre>';
        print_r($result);
        echo '</pre>';

        $insData = [];
        $insData['attribute_set_id'] = 3;
        $insData['parent_id'] = $parent_id;
        $insData['created_at'] = $this->getCurrentTime();
        $insData['updated_at'] = $this->getCurrentTime();
        //$insData['path'] = $result['path'];
        echo '<br/>' . $success = $this->resource->getConnection()->insert('catalog_category_entity', $insData);
        if ($success) {
            echo '<br/>' . $newEntityID = $this->resource->getConnection()->lastInsertId('catalog_category_entity');
            $query = "UPDATE catalog_category_entity set path = '" . $result['path'] . '/' . $newEntityID . "' where entity_id='" . $newEntityID . "'";
            $result = $this->resource->getConnection()->query($query);

            $intInsData = [];
            $intInsData['attribute_id'] = 46;
            $intInsData['store_id'] = 0;
            $intInsData['entity_id'] = $newEntityID;
            $intInsData['value'] = 0;
            $intFinalInsData[] = $intInsData;
            $intInsData = [];
            $intInsData['attribute_id'] = 53;
            $intInsData['store_id'] = 0;
            $intInsData['entity_id'] = $newEntityID;
            $intInsData['value'] = null;
            $intFinalInsData[] = $intInsData;
            $intInsData = [];
            $intInsData['attribute_id'] = 54;
            $intInsData['store_id'] = 0;
            $intInsData['entity_id'] = $newEntityID;
            $intInsData['value'] = 1;
            $intFinalInsData[] = $intInsData;

            $intInsData = [];
            $intInsData['attribute_id'] = 69;
            $intInsData['store_id'] = 0;
            $intInsData['entity_id'] = $newEntityID;
            $intInsData['value'] = 0;
            $intFinalInsData[] = $intInsData;

            echo '<pre>$intFinalInsData';
            print_r($intFinalInsData);
            echo '</pre>';
            $this->resource->getConnection()->insertMultiple('catalog_category_entity_int', $intFinalInsData);

            $varcharInsData = [];
            $varcharInsData['attribute_id'] = 45;
            $varcharInsData['store_id'] = 0;
            $varcharInsData['entity_id'] = $newEntityID;
            $varcharInsData['value'] = $categorycode;
            $varcharFinalInsData[] = $varcharInsData;

            $varcharInsData = [];
            $varcharInsData['attribute_id'] = 52;
            $varcharInsData['store_id'] = 0;
            $varcharInsData['entity_id'] = $newEntityID;
            $varcharInsData['value'] = 'PRODUCTS';
            $varcharFinalInsData[] = $varcharInsData;

            $varcharInsData = [];
            $varcharInsData['attribute_id'] = 166;
            $varcharInsData['store_id'] = 0;
            $varcharInsData['entity_id'] = $newEntityID;
            $varcharInsData['value'] = $categorycode;
            $varcharFinalInsData[] = $varcharInsData;

            echo '<pre>$varcharFinalInsData';
            print_r($varcharFinalInsData);
            echo '</pre>';
            $this->resource->getConnection()->insertMultiple('catalog_category_entity_varchar', $varcharFinalInsData);

            $textInsData = [];
            $textInsData['attribute_id'] = 45;
            $textInsData['store_id'] = 0;
            $textInsData['entity_id'] = $newEntityID;
            $textInsData['value'] = $categorycode;
            $textFinalInsData[] = $textInsData;
            echo '<pre>$varcharFinalInsData';
            print_r($textInsData);
            echo '</pre>';
            $this->resource->getConnection()->insertMultiple('catalog_category_entity_text', $textFinalInsData);

            return $newEntityID;
        } else {
            return false;
        }
    }

    public function updateCategoryDescription($categorycode, $categoryDescription)
    {
        $categoryID = $this->checkCategoryExistByIsCode($parent);
        $query = "UPDATE catalog_category_entity_text set value='" . $categoryDescription . "' where entity_id='" . $categoryID . "' and attribute_id=45";
        $result = $this->resource->getConnection()->query($query);
    }
}
