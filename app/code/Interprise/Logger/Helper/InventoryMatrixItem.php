<?php

/**
 * Created by PhpStorm.
 * User: Shadab
 * Date: 7/8/2018
 * Time: 10:02 AM
 */

namespace Interprise\Logger\Helper;

use \Interprise\Logger\Helper\Data;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Setup\Exception;

class InventoryMatrixItem extends Data
{
    public $_prices;

    public $helper_inventoryitem;

    public $product_obj;

    public $helper_category;

    public $attribute_repository;

    public $_eavSetupFactory;

    public $_objectManager;

    protected $inventoryStock;

    protected $prices;

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
        \Magento\Eav\Api\AttributeRepositoryInterface $attribute_repository,
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
        \Interprise\Logger\Helper\InventoryStock $inventorystock,
        \Interprise\Logger\Helper\Prices $_prices,
        \Interprise\Logger\Helper\InventoryItem $inventoryitem,
        \Magento\Catalog\Model\Product $product_object
    ) {
        $this->inventoryStock = $inventorystock;
        $this->prices = $_prices;
        $this->attribute_repository = $attribute_repository;
        $this->_eavSetupFactory = $eavSetupFactory;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        // $helper_factory = $this->_objectManager->get('\Magento\Core\Model\Factory\Helper');
        $this->helper_inventoryitem = $inventoryitem;
        $this->product_obj = $product_object;
        //$this->helper_category = $this->_objectManager->get('\Interprise\Logger\Helper\Category');
        $this->helper_category = $this->helper_inventoryitem->category;
        parent::__construct($context,
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

    public function InventoryMatrixItem_single($data)
    {
        ini_set("display_errors", "1");
        echo '<br/>' . __METHOD__ . '<br/>';
        $dataId = $data['DataId'];
        //$api_responsc = $this->getCurlData('inventory/' . $dataId);
        $update_data['ActivityTime'] = $this->getCurrentTime();
        $update_data['Request'] = 'To call endpoint of Matrix Group';
        $update_data['Response'] = '';
        if ($this->checkDepartmentFromInterprise($dataId)) {
            $result = $this->createProductMatrix($dataId);
            if ($result['Status']) {
                $update_data['Status'] = 'Success';
                $update_data['Remarks'] = 'Success';
            } else {
                $update_data['Status'] = 'fail';
                $update_data['Remarks'] = json_encode($result['error']);
            }
        } else {
            $update_data['Status'] = 'Department not match';
            $update_data['Remarks'] = json_encode('Department does not match');
        }

        return $update_data;
    }

    public function createProductMatrix($data_id)
    {
        echo '<br/>' . __METHOD__ . '<br/>';
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $directory = $objectManager->get('\Magento\Framework\Filesystem\DirectoryList');
        $media_path = $directory->getRoot() . '/pub/media/isimages/';
        if (!defined('UPLOAD_DIR')) {
            define('UPLOAD_DIR', $media_path);
        }

        $create_group_product = $this->create_matrix_group(['data_id' => $data_id]);
        if (!$create_group_product['Status']) {
            return $create_group_product;
        }
        echo '<br/>$group_product_id' . $group_product_id = $create_group_product['entity_id'];
        $set_product = $this->productfactory->create()->setStoreId(0)->load($group_product_id);
        $api_responsc = $this->getCurlData('inventory/matrix/' . $data_id . "/detail");
        if (!$api_responsc['api_error']) {
            return $api_responsc;
        }
        $attribute_data = $api_responsc['results']['data'];

        $this->create_attribute($attribute_data);
        $config_attributes = [];
        if (count($attribute_data) > 0) {
            foreach ($attribute_data as $key => $value) {
                $attribute = $value['attributes'];
                if (isset($attribute['attributeCode1'])) {
                    //$code = $this->create_new_attribute_name($attribute['attributeCode1']);

                    $attribute_1_data = $this->getMagentoAttributeCode($attribute['attributeCode1']);

                    if ($attribute_1_data['status'] == 'success') {
                        $code = $attribute_1_data['magento_attribute_code'];
                        //$code = 'color';
                        $attribute_id = $this->checkExistAttribute($code);
                        if ($attribute_id) {
                            $config_attributes[] = $attribute_id;
                        }
                    }
                }
                if (isset($attribute['attributeCode2'])) {
                    //$code = $this->create_new_attribute_name($attribute['attributeCode2']);
                    //$code = 'Sizes';
                    $attribute_2_data = $this->getMagentoAttributeCode($attribute['attributeCode2']);
                    if ($attribute_2_data['status'] == 'success') {
                        $code = $attribute_2_data['magento_attribute_code'];
                        //$code = 'color';
                        $attribute_id = $this->checkExistAttribute($code);
                        if ($attribute_id) {
                            $config_attributes[] = $attribute_id;
                        }
                    }
                }
//                if (isset($attribute['attributeCode3'])) {
//                    $code = $this->create_new_attribute_name($attribute['attributeCode3']);
//                    $attribute_id = $this->checkExistAttribute($code);
//                    if ($attribute_id) {
//                        $config_attributes[] = $attribute_id;
//                    }
//                }
//                if (isset($attribute['attributeCode4'])) {
//                    $code = $this->create_new_attribute_name($attribute['attributeCode4']);
//                    $attribute_id = $this->checkExistAttribute($code);
//                    if ($attribute_id) {
//                        $config_attributes[] = $attribute_id;
//                    }
//                }
            }
        }
        //echo '<br/>$config_attributes';
        //$this->prf($config_attributes);
//        $this->prf($attribute_data);

        $config_attributes = array_unique($config_attributes);
        //$set_product->getTypeInstance()->setUsedProductAttributeIds($config_attributes, $set_product); //attribute ID of attribute 'size_general' in my store
        // $configurableAttributesData = $set_product->getTypeInstance()->getConfigurableAttributesAsArray($set_product);
        //$set_product->setCanSaveConfigurableAttributes(true);
        //$set_product->setConfigurableAttributesData($configurableAttributesData);

        print_r(["PRODUCTID" => $set_product->getId()]);
//        file_put_contents("/var/www/html/var/interprise.log", json_encode(["PRODUCTID" => $set_product->getId()]) . "\n", FILE_APPEND);
        try {
            $set_product->save();
        } catch (Exception $ex) {
            echo $ex->getMessage() . ' in function ' . __METHOD__;
        }

        //$assignConfigAttribute = $this->setConfigurableAttribute($group_product_id,array(232));
        //$assignConfigAttribute = $this->setConfigurableAttribute($group_product_id, $config_attributes);

        $products_ids = $this->create_matrix_item($attribute_data);

        //echo "matriiiixxxxx---";

        echo '<pre>';
        print_r($products_ids);
        echo '</pre>';
        if (!$products_ids['Status']) {
            return $products_ids;
        }
        //if ($products_ids['Status']) {
        echo "grouop product iddsdsfsdsdsfsf::::$group_product_id";
        //$this->prf($products_ids);

        $associated_product_ids = array_unique($products_ids['entity_id']);
        print_r(['$associated_product_ids' => $associated_product_ids]);
        $this->prf($associated_product_ids);
        $this->prf($config_attributes);

        $assignSimpleToConfigureProduct = $this->assignSimpleToConfigureProductTest($group_product_id, $config_attributes, $associated_product_ids);

        //$result_price = $this->getAssociatedProdcuctPrice($group_product_id);

        if ($assignSimpleToConfigureProduct) {
            $status['Status'] = true;
            $status['error'] = '';
            $status['entity_id'] = $group_product_id;

            return $status;
        } else {
            $status['Status'] = false;
            $status['error'] = 'Product not assigned to Config product';
            $status['entity_id'] = '';

            return $status;
        }
    }

    public function create_matrix_group($data)
    {
        echo '<br/>Method: ' . __METHOD__ . '<br/>';
        $dataId = $data['data_id'];
        $product_found = $this->checkProductExistByItemCode($dataId);
//        if (!$product_found) {
//            $productRepository = ObjectManager::getInstance()->get(\Interprise\Logger\Helper\InventoryItem::class);
//            $productRepository->insertItemNew($dataId);
//
//            die("SS");
//        }
//        die("BBB");
        if ($product_found) {//overwrite

            $product_check = $this->productfactory->create()->setStoreId(0)->load($product_found);
            /* if($product_check->getAttributeSetId()==9){
              $status['status'] = false;
              $status['error'] = 'Item Can not be updated until reviewed. means it is still in New Item attribute set';
              $status['entity_id'] = '';
              return $status;
              } */
            $product_group_id = $product_found;
            $results_group = $this->updateMatrixGroupItem($product_group_id, $data['data_id'], $data);
            $status['Status'] = true;
            $status['error'] = '';
            $status['entity_id'] = $product_group_id;

            return $status;
        }
        $api_responsc = $this->getCurlData('inventory/' . $dataId);
        if (!$api_responsc['api_error']) {
            $status['Status'] = false;
            $status['error'] = ['Api_Error' => $api_responsc['results']];
            $status['entity_id'] = '';

            return $status;
        }

        $attribute_data = $api_responsc['results']['data']['attributes'];
        /* $validat_data = $this->validate_field->validations('item_details', $attribute_data);
          if (count($validat_data)) {
          $status['status'] = false;
          $status['error'] = array('item_details' => $validat_data);
          $status['entity_id'] = '';
          return $status;
          } */
        $return_data = $this->creationOfMatrixGroup_new($attribute_data);

        return $return_data;
    }

    public function updateMatrixGroupItem($product_id, $data_id, $data = [])
    {
        echo '<br/>Method: ' . __METHOD__ . '<br/>';

        $update_product = $this->productfactory->create()->setStoreId(0)->load($product_id);
        $description = $this->helper_inventoryitem->getItemDescription($data_id);

        $update_product->setName($description['itemDescription']);

        $isitemcode = $update_product->getData('interprise_item_code');
        $prices = $this->prices->getAllPrices($update_product->getSku(), $isitemcode, 'GBP');
        $price = $prices['price'];
        $msrp = $prices['msrp'];
        //$special_price = $prices['special_price'];
        if ($msrp > 0) {
        } else {
            $msrp = null;
        }
        // if ($special_price >= $price) {
        //     $special_price = NULL;
        // }

        //$wholesale_price = $prices['wholesale_price'];
        $update_product->setData('group_price', []);
        $update_product->save();
        $update_product->setPrice($price);
        //$update_product->setSpecialPrice($special_price);
        $update_product->setMsrp($msrp);

        /*$api_responsc_new = $this->api_helper->getCurlData('inventory/' . $data_id);
        if ($api_responsc_new['api_error']) {
            $attribute_data = $api_responsc_new['results']['data']['attributes'];
            $p_status = $attribute_data['status'];
            if (in_array(strtolower($p_status), array('a', 'p'))) {
                //$update_product->setStatus(1);
            } else {
                $update_product->setStatus(2);
            }
        }*/

        $api_responsc = $this->getCurlData('inventory/' . $data_id);
        if (!$api_responsc['api_error']) {
            $status['Status'] = false;
            $status['error'] = ['Api_Error' => $api_responsc['results']];
            $status['entity_id'] = '';

            return $status;
        }

        $data = $api_responsc['results']['data']['attributes'];

//        echo '<pre>';
//        print_r($data);
//        echo '</pre>';
//        die;

        /////////////////////////// Edited By Manisha to get value of isfeatured and published according to websitecode ////////////////////////
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $isFeatured = '';
        $isPublished = 1;
        $webSiteCode = 'WEB-000001';
        $api_response_web_new = $this->getCurlData('inventory/weboption/' . $data_id);
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

        $update_product->setIsfeatured($isFeatured);
        $update_product->setPublished($isPublished);

//        $mfgcode="";
//        if(isset($data['manufacturerCode'])){
//            $update_product->setManufacturercode($data['manufacturerCode']);
//            $mfgcode=$data['manufacturerCode'];
//        }

        $update_product->setInterpriseStatus($data['status']);
        if ($data['status'] == "D") {
            $update_product->setStatus(2);
        } else {
            if ($isPublished == '1') {
                $update_product->setStatus(1);
            } else {
                $update_product->setStatus(2);
            }
        }

        if ($data['salesTaxOption'] == 'Customer') {
            $update_product->setData('is_vatexempt', 1);
        } else {
            $update_product->setData('is_vatexempt', 0);
        }

        ///////////////////////////////////////////////////// End Code ////////////////////////////////////////////////////////////////////////

        ///////////////////////// Edited By Manisha to add Customfields ////////////////////////////////

        $custom_fields = [
            '_3dsupport_c', 'audiblenoise_c', 'brightness_c', 'builtinaudio_c', 'business_c', 'contrast_c', 'education_c', 'flatscreenaspectratio_c', 'flatscreenbrightness_c', 'flatscreencontrast_c', 'flatscreenfeatures_c', 'flatscreenincluded_c', 'flatscreeninputs_c', 'flatscreenoutputs_c', 'gtin_c', 'hdmi_c', 'homecinema_c', 'imagesize_c', 'includedprojector_c', 'includedsoftware_c', 'inputs_c', 'installationavailable_c', 'nextdaydelivery_c', 'itemsincluded_c', 'keystonecorrection_c', 'lampcategory_c', 'lamplife_c', 'largevenue_c', 'lens_c', 'lensshift_c', 'outputs_c', 'portable_c', 'powerconsumption_c', 'projectordimensions_c', 'projectormanufacturer_c', 'projectorwarranty_c', 'projectorweight_c', 'rj45_c', 'screensurface_c', 'includedsoftware_c', 'screenweight_c', 'shortthrow_c', 'staffpick_c', 'throwdistance_c', 'throwratio_c', 'touchtype_c', 'flatscreendisplaytype_c', 'ultraportable_c', 'ultrashortthrow_c', 'usb_c', 'activescreenarea_c', 'screenwarranty_c', 'flatscreenweight_c', 'wirelessnetworking_c', 'activescreenarea_c', 'tags_iuk', 'allowtimeddelivery_c', 'screen_warranty_c', 'ean_c',
        ];

        $custom_fields_dropdown = ['aspectratio_c', 'nativeresolution_c', 'resolution_c', 'displaytype_c', 'physicalscreensize_c', 'flatscreensize_c', 'projectormodel_c'];
        if (isset($data['customFields']) && count($data['customFields']) > 0) {
            foreach ($data['customFields'] as $key => $item) {
                if (in_array(strtolower($item['field']), $custom_fields)) {
                    //echo strtolower($item['field'])." = ".$item['value']."</br>";

                    $update_product->setData(strtolower($item['field']), $item['value']);
                } else if (in_array(strtolower($item['field']), $custom_fields_dropdown)) {
                    $attributeCode = strtolower($item['field']);
                    $eavConfig = $objectManager->get('\Magento\Eav\Model\Config');
                    $attribute = $eavConfig->getAttribute('catalog_product', strtolower($item['field']));
                    $optionID = $attribute->getSource()->getOptionId($item['value']);
                    if (isset($optionID) && $optionID != '') {
                        $update_product->setData(strtolower($item['field']), $optionID);
                    } else {
                        $attributeID = $attribute->getId();
                        $attributeOptionManagement = $objectManager->get('\Magento\Eav\Api\AttributeOptionManagementInterface');
                        $optionLabelFactory = $objectManager->get('\Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory');
                        $optionFactory = $objectManager->get('\Magento\Eav\Api\Data\AttributeOptionInterfaceFactory');

                        $optionLabel = $optionLabelFactory->create();
                        $optionLabel->setStoreId(0);
                        $optionLabel->setLabel($item['value']);

//                                $optionLabel1 = $optionLabelFactory->create();
//                                $optionLabel1->setStoreId(1);
                        //                               $optionLabel1->setLabel($item['value']);

                        $option = $optionFactory->create();
                        $option->setLabel($optionLabel);
                        $option->setStoreLabels([$optionLabel]);
                        $option->setSortOrder(0);
                        $option->setIsDefault(false);

                        $attributeOptionManagement->add(
                            'catalog_product',
                            $attributeCode,
                            $option
                        );
                        $attribute = $eavConfig->getAttribute('catalog_product', $attributeCode);
                        $optionID = $attribute->getSource()->getOptionId($item['value']);
                        $update_product->setData(strtolower($item['field']), $optionID);
                    }
                }
            }
        }

        /////////////////////// Code to add manufacturercode //////////////////////////////////

//        $eavConfig = $objectManager->get('\Magento\Eav\Model\Config');
//        $attribute = $eavConfig->getAttribute('catalog_product', 'manufacturercode');
//        $optionID = $attribute->getSource()->getOptionId($mfgcode);
//        if(isset($optionID) && $optionID!=''){
//            $update_product->setData('manufacturercode', $optionID);
//        } else{
//            $attributeID = $attribute->getId();
//            $attributeOptionManagement =  $objectManager->get('\Magento\Eav\Api\AttributeOptionManagementInterface');
//            $optionLabelFactory = $objectManager->get('\Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory');
//            $optionFactory = $objectManager->get('\Magento\Eav\Api\Data\AttributeOptionInterfaceFactory');
//
//            $optionLabel = $optionLabelFactory->create();
//            $optionLabel->setStoreId(0);
//            $optionLabel->setLabel($mfgcode);
//
//            $optionLabel1 = $optionLabelFactory->create();
//            $optionLabel1->setStoreId(1);
//            $optionLabel1->setLabel($mfgcode);
//
//            $option = $optionFactory->create();
//            $option->setLabel($optionLabel);
//            $option->setStoreLabels([$optionLabel, $optionLabel1]);
//            $option->setSortOrder(0);
//            $option->setIsDefault(false);
//
//            $attributeOptionManagement->add(
//                'catalog_product',
//                   'manufacturercode',
//                   $option
//            );
//            $attribute = $eavConfig->getAttribute('catalog_product','manufacturercode');
//            $optionID = $attribute->getSource()->getOptionId($mfgcode);
//            $update_product->setData('manufacturercode', $optionID);
//
//        }

        //////////////////////////// end code //////////////////////////////////////////

        $update_product->setJson(json_encode($data));

        /////////////////////////////////// End Custom fields ////////////////////////////////////////////////

        $update_product->save();

        //$this->updateWhosalePrice($update_product);
        $this->processProductStatus($update_product);
    }

    public function creationOfMatrixGroup_new($data)
    {
        echo '<br/>Method: ' . __METHOD__ . '<br/>';
        $dataId = $data['itemCode'];
        try {
            $api_response = $this->getCurlData('inventory/all?itemcode=' . $dataId);
            echo '<pre>';
            print_r($api_response);
            echo '</pre>';
            $api_response_web = $this->getCurlData('inventory/weboption/' . $dataId . '/description');
            $update_data['ActivityTime'] = $this->getCurrentTime();
            $update_data['Request'] = $api_response['request'];
            $update_data['Response'] = json_encode($api_response['results']['data']);
            if ($api_response['api_error']) {
                $data = $api_response['results']['data']['attributes'];
                if (!isset($data)) {
                    $update_data['Status'] = false;
                    $update_data['Remarks'] = 'Attribute data not found in API response for dataid ' . $dataId;
                } else {
                    $product_name = $data['description'][0]['itemDescription'];
                    $product_description = (isset($data['description'][0]['extendedDescription']) ? $data['description'][0]['extendedDescription'] : $product_name);
                    $seTitle = $product_name;
                    $seKeywords = $product_name;
                    $seDescription = $product_description;
                    $webDescription = $product_description;
                    if ($api_response_web['api_error']) {
                        $data_web = $api_response_web['results']['data'][0]['attributes'];
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
                    } else {
                        $update_data['Status'] = 'Fail';
                        $update_data['Remarks'] = "Unexpected response from the weboption API";
                    }
                    try {
                        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

                        $product_sku = $data['itemName'];
                        try {
                            $productRepository = ObjectManager::getInstance()->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
                            $set_product = $productRepository->get($product_sku, true, null, true);
                        } catch (NoSuchEntityException) {
                            $set_product = $objectManager->create('\Magento\Catalog\Model\Product');
                        }

                        $isitemcode = $data['itemCode'];

                        /////////////////////////// Edited By Manisha to get value of isfeatured and published according to websitecode ////////////////////////
                        $isFeatured = '';
                        $isPublished = 1;
                        $webSiteCode = 'WEB-000001';
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

                        $set_product->setIsfeatured($isFeatured);
                        $set_product->setPublished($isPublished);
                        ///////////////////////////////////////////////////// End Code ////////////////////////////////////////////////////////////////////////

                        $set_product->setMetaTitle($seTitle);
                        $set_product->setMetaKeyword($seKeywords);
                        $set_product->setMetaDescription($product_description);
//                        $set_product->setDescription($webDescription);
                        //$set_product->setShortDescription($seDescription);
                        $prices = $this->helper_inventoryitem->getAllPricesNew($data, $product_sku, $isitemcode, 'GBP');
                        $set_product->setWebsiteIds([1, 2]);
                        $set_product->setStoreId(0);
                        $set_product->setAttributeSetId(4);
                        $set_product->setTypeId('configurable');
                        $set_product->setCreatedAt(strtotime('now'));
                        $set_product->setName($product_name);
                        $set_product->setSku($product_sku);
                        $set_product->setInterpriseItemCode($isitemcode);
                        $set_product->setInterpriseStatus($data['item']['status']);
                        if (isset($data['unitOfMeasure'][0]['netWeightInKilograms']) && $data['unitOfMeasure'][0]['netWeightInKilograms'] > 0) {
                            $set_product->setWeight($data['unitOfMeasure'][0]['netWeightInKilograms']);
                        } else if (isset($data['unitOfMeasure'][0]['weightInKilograms']) && $data['unitOfMeasure'][0]['weightInKilograms'] > 0) {
                            $set_product->setWeight($data['unitOfMeasure'][0]['weightInKilograms']);
                        } else {
                            $set_product->setProductHasWeight(0);
                            $set_product->setWeight(0);
                        }
                        $set_product->setData('interprise_item_type', $data['item']['itemType']);
                        $unitmeasurecode = $data['unitOfMeasure'][0]['unitMeasureCode'];
                        $set_product->setData('is_unitmeasurecode', $unitmeasurecode);
                        if (isset($data['unitOfMeasure'][0]['upccode'])) {
                            $set_product->setUpccode($data['unitOfMeasure'][0]['upccode']);
                        }

                        $mfgcode = "";
                        if (isset($data['item']['manufacturerCode'])) {
                            //$set_product->setManufacturercode($data['item']['manufacturerCode']);
                            $mfgcode = $data['item']['manufacturerCode'];
                        }
                        //$set_product->setStatus(1);

                        if ($data['item']['status'] == "D") {
                            $set_product->setStatus(2);
                        } else {
                            if ($isPublished == '1') {
                                $set_product->setStatus(1);
                            } else {
                                $set_product->setStatus(2);
                            }
                        }

                        if (isset($data['category'])) {
                            $categories = $data['category'];
                            print_r(['CATEGORIES' => $categories]);
                            $category_ids = [];
                            foreach ($categories as $key => $value) {
                                $catExisting = $this->checkCategoryExistByIsCode($value["categoryCode"]);
                                if (!$catExisting) {
//                                    file_put_contents("/var/www/html/var/interprise.log", json_encode(['notFoundCategory' => $value]) . "\n", FILE_APPEND);
                                    print_r(['notFoundCategory' => $value]);
                                }
                                $category_ids = array_merge($category_ids, $catExisting);
                            }
                        }

                        if (!isset($category_ids) || !$category_ids || $category_ids[0] == '') {
                            $parent_id_default = $this->getConfig('setup/general/defaultcategory');
                            $category_ids = [$parent_id_default];
                        }
                        echo '<pre>$category_ids';
                        print_r($category_ids);
                        echo '</pre>';
//                        $set_product->setCategoryIds($category_ids);
                        $set_product->setTaxClassId(2);

                        if ($data['item']['salesTaxOption'] == 'Customer') {
                            $set_product->setData('is_vatexempt', 1);
                        } else {
                            $set_product->setData('is_vatexempt', 0);
                        }

                        $set_product->setVisibility(4);
                        $set_product->setNewsToDate($prices['dateTo']);
                        $set_product->setCountryOfManufacture('UK');
                        $set_product->setPrice($prices['price']);
                        $set_product->setData('is_retailprice', $prices['retail_price']);
                        $set_product->setData('is_wholesaleprice', $prices['wholesale_price']);
//                        if (isset($prices['special_price']) && $prices['special_price'] != '') {
//                            $set_product->setSpecialPrice($prices['special_price']);
//                        } else {
//                            $set_product->setSpecialPrice('');
//                        }
//                        if (isset($prices['dateFrom']) && $prices['dateFrom'] != '') {
//                            $set_product->setSpecialFromDate($prices['dateFrom']);
//                        }
//                        if (isset($prices['dateTo']) && $prices['dateTo'] != '') {
//                            $set_product->setSpecialToDate($prices['dateTo']);
//                        }
                        $set_product->setMsrpEnabled(1);
                        $set_product->setMsrpDisplayActualPriceType(1);
                        $set_product->setMsrp($prices['msrp']);
//                        $name_url  =$product_sku.$isitemcode;
                        $counter = $data['item']['counter'];
                        $url = preg_replace('#[^0-9a-z]+#i', '-', 'p' . '-' . $counter . '-' . $product_name);
//			echo $url;
//			$url = preg_replace('#[^0-9a-z]+#i', '-', $name_url);
                        $url = strtolower($url);
                        $set_product->setUrlKey($url);
                        $set_product->setStockData(
                            [
                                'use_config_manage_stock' => 1,
                                'manage_stock' => 1, // manage stock
                                'min_sale_qty' => 1, // Shopping Cart Minimum Qty Allowed
                                'max_sale_qty' => 99999, // Shopping Cart Maximum Qty Allowed
                                'is_in_stock' => 1, // Stock Availability of product
                                'qty' => 999, // qty of product
                            ]
                        );

                        ///////////////////////// Edited By Manisha to add Customfields ////////////////////////////////

                        $custom_fields = ['webid_c'];

                        $custom_fields_dropdown = [];
                        if (isset($data['item']['customFields']) && count($data['item']['customFields']) > 0) {
                            foreach ($data['item']['customFields'] as $key => $item) {
                                if (in_array(strtolower($item['field']), $custom_fields)) {
                                    //echo strtolower($item['field'])." = ".$item['value']."</br>";

                                    $set_product->setData(strtolower($item['field']), $item['value']);
                                } else if (in_array(strtolower($item['field']), $custom_fields_dropdown)) {
                                    $attributeCode = strtolower($item['field']);
                                    $eavConfig = $objectManager->get('\Magento\Eav\Model\Config');
                                    $attribute = $eavConfig->getAttribute('catalog_product', strtolower($item['field']));
                                    $optionID = $attribute->getSource()->getOptionId($item['value']);
                                    if (isset($optionID) && $optionID != '') {
                                        $set_product->setData(strtolower($item['field']), $optionID);
                                    } else {
                                        $attributeID = $attribute->getId();
                                        $attributeOptionManagement = $objectManager->get('\Magento\Eav\Api\AttributeOptionManagementInterface');
                                        $optionLabelFactory = $objectManager->get('\Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory');
                                        $optionFactory = $objectManager->get('\Magento\Eav\Api\Data\AttributeOptionInterfaceFactory');

                                        $optionLabel = $optionLabelFactory->create();
                                        $optionLabel->setStoreId(0);
                                        $optionLabel->setLabel($item['value']);

                                        //   $optionLabel1 = $optionLabelFactory->create();
                                        //   $optionLabel1->setStoreId(1);
                                        //   $optionLabel1->setLabel($item['value']);

                                        $option = $optionFactory->create();
                                        $option->setLabel($optionLabel);
                                        $option->setStoreLabels([$optionLabel]);
                                        $option->setSortOrder(0);
                                        $option->setIsDefault(false);

                                        $attributeOptionManagement->add(
                                            'catalog_product',
                                            $attributeCode,
                                            $option
                                        );
                                        $attribute = $eavConfig->getAttribute('catalog_product', $attributeCode);
                                        $optionID = $attribute->getSource()->getOptionId($item['value']);
                                        $set_product->setData(strtolower($item['field']), $optionID);
                                    }
                                }
                            }
                        }

                        /////////////////////// Code to add manufacturercode //////////////////////////////////

//                        $eavConfig = $objectManager->get('\Magento\Eav\Model\Config');
//                        $attribute = $eavConfig->getAttribute('catalog_product', 'manufacturercode');
//                        $optionID = $attribute->getSource()->getOptionId($mfgcode);
//                        if(isset($optionID) && $optionID!=''){
//                            $set_product->setData('manufacturercode', $optionID);
//                        } else{
//                            $attributeID = $attribute->getId();
//                            $attributeOptionManagement =  $objectManager->get('\Magento\Eav\Api\AttributeOptionManagementInterface');
//                            $optionLabelFactory = $objectManager->get('\Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory');
//                            $optionFactory = $objectManager->get('\Magento\Eav\Api\Data\AttributeOptionInterfaceFactory');
//
//                            $optionLabel = $optionLabelFactory->create();
//                            $optionLabel->setStoreId(0);
//                            $optionLabel->setLabel($mfgcode);
//
//                            $optionLabel1 = $optionLabelFactory->create();
//                            $optionLabel1->setStoreId(1);
//                            $optionLabel1->setLabel($mfgcode);
//
//                            $option = $optionFactory->create();
//                            $option->setLabel($optionLabel);
//                            $option->setStoreLabels([$optionLabel, $optionLabel1]);
//                            $option->setSortOrder(0);
//                            $option->setIsDefault(false);
//
//                            $attributeOptionManagement->add(
//                                'catalog_product',
//                                   'manufacturercode',
//                                   $option
//                            );
//                            $attribute = $eavConfig->getAttribute('catalog_product','manufacturercode');
//                            $optionID = $attribute->getSource()->getOptionId($mfgcode);
//                            $set_product->setData('manufacturercode', $optionID);
//
//                        }

                        //////////////////////////// end code //////////////////////////////////////////

                        $set_product->setJson(json_encode($data));

                        /////////////////////////////////// End Custom fields ////////////////////////////////////////////////
                        $set_product->save();

//                        $this->updateWhosalePrice($set_product);
                        $this->processProductStatus($set_product);

//                        if (isset($data['item']['photo'])) {
//                            $img_field = $data['item']['photo'];
//                            $img_path = $this->getImages($img_field);
//                            $product = $objectManager->create('Magento\Catalog\Model\Product')->load($set_product->getId());
//                            $mediaGalleryProcessor = $objectManager->get('Magento\Catalog\Model\Product\Gallery\Processor');
//                            $mediaGalleryProcessor->addImage($product, $img_path, ['image', 'thumbnail', 'small_image'], false, false);
//                            $product->save();
//                            unlink($img_path);
//                        }

                        /////////////////////////// Code for Images from custom fields ///////////////////////////////////////

//                if (isset($data['item']['customFields']) && count($data['item']['customFields']) > 0) {
//                    $base_path = "https://www.avpartsmaster.co.uk/images/product";
//                    $custom_fields = ['imageoverridelarge_c', 'imageoverridemedium_c', 'imageoverrideicon_c'];
//                    $sizes = ['large', 'medium', 'icon'];
//                    $i = 0;
//                    foreach ($data['item']['customFields'] as $key => $item) {
//                        if (in_array(strtolower($item['field']), $custom_fields)) {
//
//                            $img_url = $base_path . '/' . $sizes[$i] . '/' . $item['value'];
//                            $img_path = $this->getcustomimages($img_url);
//                            if ($img_path != false) {
//                                $mediaGalleryProcessor = $objectManager->get('Magento\Catalog\Model\Product\Gallery\Processor');
//                                $mediaGalleryProcessor->addImage($set_product, $img_path, array('image', 'thumbnail', 'small_image'), false, false);
//                                $set_product->save();
//                                unlink($img_path);
//                                break;
//                            }
//                            $i = $i + 1;
//                        }
//                    }
//                }

                        //////////////////////////////////// End Code ///////////////////////////////////////////////////////
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
                        $update_data['Remarks'] = "Err msg - " . $err_msg . " in function " . __METHOD__;
                    } catch (UrlAlreadyExistsException $e1) {
                        $err_msg = $e1->getMessage();
                        $update_data['Status'] = 'Fail';
                        $update_data['Remarks'] = "Err msg1 - " . $err_msg . " in function " . __METHOD__;
                    } catch (AlreadyExistsException $e2) {
                        $err_msg = $e2->getMessage();
                        $update_data['Status'] = 'Fail';
                        $update_data['Remarks'] = "Err msg2 - " . $err_msg . " in function " . __METHOD__;
                    } catch (Magento\Framework\DB\Adapter\DuplicateException $e3) {
                        $err_msg = $e3->getMessage();
                        $update_data['Status'] = 'Fail';
                        $update_data['Remarks'] = "Err msg3 - " . $err_msg . " in function " . __METHOD__;
                    }
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

    public function getImages($img)
    {
        if (!is_dir(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR);
        }
        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data1 = base64_decode($img);
        $image_name = uniqid() . '.png';
        $file = UPLOAD_DIR . $image_name;
        $success = file_put_contents($file, $data1);
        chmod($file, 0777);
        $success ? $file : false;

        return $file;
        //return $image_name;
    }

    public function create_attribute($attribute_data)
    {
        $attribute_arr = [];
        $attribute_code = [];
        $attribute_lable = [];
        if (count($attribute_data) > 0) {
            foreach ($attribute_data as $key => $value) {
                $attribute = $value['attributes'];
                // if (!$attribute['selected']) {
                //     continue;
                // }//only those attribute will be created which are selected
                if (isset($attribute['attributeCode1'])) {
                    $attribute_1_data = $this->getMagentoAttributeCode($attribute['attributeCode1']);

                    if ($attribute_1_data['status'] == 'success') {
                        $attribute_1 = $attribute_1_data['magento_attribute_code'];
                        $attribute_code[$attribute_1][] = $attribute['attribute1Description'];
                        if ($attribute_1 == 'color') {
                            $attribute_code['filter_colour'][] = $attribute['attribute1Description'];
                        }
                    }
                }
                if (isset($attribute['attributeCode2'])) {
                    $attribute_2_data = $this->getMagentoAttributeCode($attribute['attributeCode2']);

                    if ($attribute_2_data['status'] == 'success') {
                        $attribute_2 = $attribute_2_data['magento_attribute_code'];
                        $attribute_code[$attribute_2][] = $attribute['attribute2Description'];
                    }
                    //$attribute_code['sizes_dropdown'][] = $attribute['attribute2Description'];
                }
//                if (isset($attribute['attributeCode3'])) {
//                    $attribute_code[$attribute['attributeCode3']][] = $attribute['attribute3Description'];
//                }
//                if (isset($attribute['attributeCode4'])) {
//                    $attribute_code[$attribute['attributeCode4']][] = $attribute['attribute4Description'];
//                }
            }
        }
        $this->prf('$attribute_code');
        //$this->prf(__METHOD__);
        $this->prf($attribute_code);
        // die('end right');
        if (count($attribute_code) > 0) {
//            foreach ($attribute_code as $key => $value) {
//                $label = $key;
//                //$code = preg_replace('#[^0-9a-z]+#i', '_', strtolower($key));
//                $code = $this->create_new_attribute_name($key);
//                $attribute_id = $this->checkExistAttribute($code);
//                if (!$attribute_id) {
//                    $this->createAttribute($code, $label);
//                }
//            }

            foreach ($attribute_code as $_option => $_options) {
                $code_opt = $this->create_new_attribute_name($_option);
                $attribute_id = $this->checkExistAttribute($code_opt);
                if ($attribute_id) {
                    $options = array_unique($_options);
                    if (count($options) > 0) {
                        $this->addAttributeValues($attribute_id, $code_opt, $options);
                    }
                }
            }
        }
    }

    public function getMagentoAttributeCode($interpriceAttributeCode = '')
    {
        $query = "SELECT `magento_attribute_code` FROM `interprise_attribute_mapping` WHERE LOWER(`interprise_attribute_code`)='" . trim(strtolower($interpriceAttributeCode)) . "' AND STATUS=1";

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $result = $connection->fetchRow($query);
        if ($result) {
            $finalResult['status'] = 'success';
            $finalResult['magento_attribute_code'] = $result['magento_attribute_code'];
        } else {
            $finalResult['status'] = 'fail';
        }

        return $finalResult;
    }

    public function checkExistAttribute($code)
    {
        $query = "SELECT * FROM `eav_attribute` WHERE attribute_code='$code' AND entity_type_id=4";
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $result = $connection->fetchRow($query);
        if ($result) {
            return $result['attribute_id'];
        } else {
            return;
        }
    }

    public function addAttributeValues($attribute_id, $attribute_code, $attribute_values)
    {
        $res = $this->getOptionlist($attribute_code);
        $attribute_values_array = [];
        if (count($res) > 0) {
            foreach ($res as $k => $vs) {
                $attribute_values_array[$vs['label']] = $vs['label'];
            }
        }
        $options = $attribute_values;
        $option = [];
        $option['attribute_id'] = $attribute_id;
        $option['values'] = [];
        $sort_order = 0;
        foreach ($options as $key => $value) {
            if (array_key_exists($value, $attribute_values_array)) {
                continue;
            }
            //$option['value'][$value][0] = $value;
            //foreach($allStores as $store){
            //$option['value'][$value][$store->getId()] = $value;
            //$option['value'][$value][1] = $value;
            //}
            $option['values'][] = $value;
            $sort_order++;
        }
        //$this->prf($option);
        $eavSetup = $this->_objectManager->create('\Magento\Eav\Setup\EavSetup');
        ////$eavSetup = $this->_eavSetupFactory->create();
        try {
            $eavSetup->addAttributeOption($option);
        } catch (Exception $e) {
            // echo $e->getMessage();
            // die(__METHOD__);
        }
    }

    public function getOptionlist($attributeCode)
    {
        $attributes = $this->attribute_repository->get(\Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE, $attributeCode);
        $options = $attributes->getSource()->getAllOptions(false);

        return $options;
    }

    public function create_matrix_item($matrix_items)
    {
        echo '<br/>Method: ' . __METHOD__ . '<br/>';
        $itme_id = [];
        $item_error_type = [];
        $associated_product_ids = [];
        if (count($matrix_items)) {
            foreach ($matrix_items as $key => $_item) {
                $data_item_data = [];
                $create_matrix = [];
                $attribute = $_item['attributes'];
                // if (!$attribute['selected']) {
                //     continue;
                // }
                $product_found = $this->checkProductExistByItemCode($attribute['matrixItemCode']); //Review this code for update matrix group also we have to update matrix item also

                echo "<br>matrixitemid---$product_found";

                if ($product_found) {
                    echo "<br/>Inside Product Found!!";
                    $data_item_data['api_error'] = true;
                } else {
                    $data_item_data = $this->getCurlData('inventory/' . $attribute['matrixItemCode']);
                }

                $this->prf($data_item_data);

                if ($data_item_data['api_error']) {
                    echo "<br/>Inside api_error!!";
                    if ($product_found) {
                        echo "<br/>Inside Product Found!!";
                        $create_matrix['Status'] = true;
                        $create_matrix['entity_id'] = $product_found;
                        $create_matrix['error'] = '';

                        $variants = [];
                        $attributeNotFound = [];
                        $attributeFailed = false;
                        if (isset($attribute['attributeCode1'])) {
                            echo "<br/>Inside AttributeCode1";
                            //$attribute_1 = 'color';

                            $attribute_1_data = $this->getMagentoAttributeCode($attribute['attributeCode1']);

                            if ($attribute_1_data['status'] == 'success') {
                                $attribute_1 = $attribute_1_data['magento_attribute_code'];
                                $option_id1 = $this->getOptionValueofattribute($attribute_1, $attribute['attribute1Description']);

                                $variants[$attribute_1] = $option_id1;
                            } else {
                                $attributeFailed = true;
                                $attributeNotFound[] = $attribute['attributeCode1'];
                            }
                        }
                        if (isset($attribute['attributeCode2'])) {
                            echo "<br/>Inside AttributeCode2";
                            //$attribute_2 = 'Sizes';

                            $attribute_2_data = $this->getMagentoAttributeCode($attribute['attributeCode2']);

                            if ($attribute_2_data['status'] == 'success') {
                                $attribute_2 = $attribute_2_data['magento_attribute_code'];
                                $option_id = $this->getOptionValueofattribute($attribute_2, $attribute['attribute2Description']);

                                $variants[$attribute_2] = $option_id;
                            } else {
                                $attributeFailed = true;
                                $attributeNotFound[] = $attribute['attributeCode2'];
                            }
                        }

                        if ($attributeFailed) {
                            $status['Status'] = false;
                            $status['error'] = 'Attribute Mapping not found for ' . json_encode($attributeNotFound);
                            $status['entity_id'] = '';

                            return $status;
                        }
                    } else {
                        $variants = [];
                        $attributeNotFound = [];
                        $attributeFailed = false;
                        if (isset($attribute['attributeCode1'])) {
                            echo "<br/>Inside AttributeCode1";
                            //$attribute_1 = 'color';

                            $attribute_1_data = $this->getMagentoAttributeCode($attribute['attributeCode1']);

                            if ($attribute_1_data['status'] == 'success') {
                                $attribute_1 = $attribute_1_data['magento_attribute_code'];
                                $option_id1 = $this->getOptionValueofattribute($attribute_1, $attribute['attribute1Description']);

                                $variants[$attribute_1] = $option_id1;
                            } else {
                                $attributeFailed = true;
                                $attributeNotFound[] = $attribute['attributeCode1'];
                            }
                        }
                        if (isset($attribute['attributeCode2'])) {
                            echo "<br/>Inside AttributeCode2";
                            //$attribute_2 = 'Sizes';

                            $attribute_2_data = $this->getMagentoAttributeCode($attribute['attributeCode2']);

                            if ($attribute_2_data['status'] == 'success') {
                                $attribute_2 = $attribute_2_data['magento_attribute_code'];
                                $option_id = $this->getOptionValueofattribute($attribute_2, $attribute['attribute2Description']);

                                $variants[$attribute_2] = $option_id;
                            } else {
                                $attributeFailed = true;
                                $attributeNotFound[] = $attribute['attributeCode2'];
                                // $status['Status'] = false;
                                // $status['error'] = 'Attribute Mapping not found for '.$attribute['attributeCode2'];
                                // $status['entity_id'] = '';
                                // return $status;
                            }

                            // $product->setData($attribute_1, $option_id);
                            //$product->setData($attribute_2, $option_id);

                        }

                        if ($attributeFailed) {
                            $status['Status'] = false;
                            $status['error'] = 'Attribute Mapping not found for ' . json_encode($attributeNotFound);
                            $status['entity_id'] = '';

                            return $status;
                        }

                        $data_item_data['DataId'] = $attribute['matrixItemCode'];
                        $create_matrix = $this->helper_inventoryitem->inventoryItemSingle($data_item_data, 1);
                    }
                    echo '$create_matrix';
                    $this->prf($create_matrix);

                    if ($create_matrix['Status'] && $create_matrix['Status'] == 'Success') {
                        //if(isset($create_matrix['entity_id'])){
                        $product_id = $create_matrix['entity_id'];
                        $associated_product_ids[] = $product_id;

                        if (!empty($variants)) {
                            echo "Inside";
                            echo '<pre>$variants';
                            print_r($variants);
                            echo '</pre>';
                            $product = $this->productfactory->create()->setStoreId(0)->load($product_id);

                            foreach ($variants as $att => $op) {
                                $product->setData($att, $op);
                            }
                            $product->save();
                        }

                        $itme_id[] = 'success';
                        //}
                        //Mage::helper('logger/Inventoryitem')->updateGroupPriceInsert($product->getId(),4,$group_pric);
                    } else {
                        //$itme_id[] = 'error';
                        $item_error_type[] = $create_matrix;
                    }
                } else {
                    //$itme_id[] = 'error';
                    $item_error_type[] = $data_item_data['results'];
                }

                //}
            }
        }
        //$this->prf('$associated_product_ids');
        //$this->prf($associated_product_ids);
        if (in_array('error', $itme_id)) {
            $status['Status'] = false;
            $status['error'] = $item_error_type;
            $status['entity_id'] = '';

            return $status;
        } else {
            $status['Status'] = true;
            $status['error'] = '';
            $status['entity_id'] = $associated_product_ids;

            return $status;
        }
    }

    public function getOptionValueofattribute($attribute_code, $attribute_value)
    {
        $query = "SELECT eaov.option_id FROM `eav_attribute` AS ea  INNER JOIN `eav_attribute_option` AS eao ON(ea.attribute_id=eao.attribute_id) INNER JOIN `eav_attribute_option_value` AS eaov ON(eao.option_id=eaov.option_id AND eaov.store_id=0 AND eaov.value='$attribute_value') WHERE ea.attribute_code='$attribute_code' AND ea.entity_type_id=4";
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $result = $connection->fetchRow($query);
        if ($result) {
            return $result['option_id'];
        } else {
            return;
        }
    }

    public function assignSimpleToConfigureProductTest($config_product_id, $config_attributes, $simple_associated_product_ids)
    {
        $productId = $config_product_id; // Configurable Product Id
        echo '<br/>' . __METHOD__ . '<br/>';
        $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $tablename = $resource->getTableName('catalog_product_super_attribute');
        $connection = $resource->getConnection();
        $query = "delete from $tablename where product_id=$config_product_id";
        $connection->query($query);

        $product = $this->productfactory->create()->load($productId); // Load Configurable Product
        $attributeModel = $this->_objectManager->create('Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute');
        $position = 0;
        //$attributes = array(141); // Super Attribute Ids Used To Create Configurable Product
        $attributes = $config_attributes; // Super Attribute Ids Used To Create Configurable Product
        //$associatedProductIds = array(2,4,5,6); //Product Ids Of Associated Products
        $associatedProductIds = $simple_associated_product_ids; //Product Ids Of Associated Products
        foreach ($attributes as $attributeId) {
            $data = ['attribute_id' => $attributeId, 'product_id' => $productId, 'position' => $position];
            $position++;
            $attributeModel->setData($data)->save();
        }
        $product->setTypeId("configurable"); // Setting Product Type As Configurable
        $product->setAffectConfigurableProductAttributes(4);
        $this->_objectManager->create('Magento\ConfigurableProduct\Model\Product\Type\Configurable')->setUsedProductAttributeIds($attributes, $product);
        $product->setNewVariationsAttributeSetId(4); // Setting Attribute Set Id
        $product->setAssociatedProductIds($associatedProductIds);// Setting Associated Products
        $product->setCanSaveConfigurableAttributes(true);
        try {
            $product->save();

            $query = "select * from $tablename where product_id=$config_product_id limit 1";

            $result = $connection->fetchRow($query);
            if ($result) {
            } else {
                if (count($config_attributes) > 0) {
                    $postition = 10;
                    foreach ($config_attributes as $k => $vs) {
                        $insert_query = "Replace into $tablename (product_id,attribute_id,position) values ($config_product_id,$vs,$postition)";
                        $inserts = $connection->query($insert_query);
                        $postition + 10;
                    }
                }
            }
        } catch (Exception $ex) {
            echo "<br>in exceptionsssss";
            $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
            $tablename = $resource->getTableName('catalog_product_super_attribute');
            $query = "select * from $tablename where product_id=$config_product_id limit 1";
            $connection = $resource->getConnection();
            $result = $connection->fetchRow($query);
            if ($result) {
            } else {
                if (count($config_attributes) > 0) {
                    $postition = 10;
                    foreach ($config_attributes as $k => $vs) {
                        $insert_query = "Replace into $tablename (product_id,attribute_id,position) values ($config_product_id,$vs,$postition)";
                        $inserts = $connection->query($insert_query);
                        $postition + 10;
                    }
                }
            }
            echo $ex->getMessage();
        }
        $arr = [];
        $arr['Status'] = true;
        $arr['entity_id'] = $config_product_id;

        return $arr;
    }

    public function assignSimpleToConfigureProduct($product_id, $configurabledata, $simpleProducts)
    {
        echo "<br>" . __METHOD__ . "$product_id" . "<br>";
        //    echo '<pre>';
        //    print_r($configurabledata);
        //    echo '</pre>';
        //    echo '<pre>$simpleProducts';
        //    print_r($simpleProducts);
        //    echo '</pre>';
        try {
            $configurable_product = $this->productfactory->create()->load($product_id);
            $attributeModel = $this->_objectManager->create('Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute');
            $configurableProductsData = [];
            $price_arr = [];
            $group_price_arr = [];
            if (count($simpleProducts) > 0) {
                foreach ($simpleProducts as $key => $product_id) {
                    $_child = $this->productfactory->create()->load($product_id);
                    $price = $_child->getPrice();
                    $price_arr[$product_id] = $price;
                }
            }
            $position = 0;
            foreach ($configurabledata as $attributeId) {
                $data = ['attribute_id' => $attributeId, 'product_id' => $product_id, 'position' => $position];
                print_r($data);
                $position++;
                //$attributeModel->setData($data)->save();
            }

            $this->prf($price_arr);
            $min_price = 20;
            if (count($simpleProducts) > 0) {
                foreach ($simpleProducts as $key => $product_id) {
                    $this->prf($product_id);

                    if (count($configurabledata)) {
                        foreach ($configurabledata as $key_con => $value_con) {
                            $_child = $this->productfactory->create()->load($product_id);
                            if ($value_con == '93') {
                                $optionLabel = $_child->getAttributeText('color');
                                $optionID = $_child->getData('color');
                            } else {
                                $optionLabel = $_child->getAttributeText('Sizes');
                                $optionID = $_child->getData('Sizes');
                            }

                            $price_percent = $this->calculatePercentage($min_price, $price_arr[$product_id]);
                            $configurableProductsData[$product_id][] = [
                                'label' => $optionLabel, //attribute label
                                'attribute_id' => $value_con, //attribute ID of attribute 'color' in my store
                                'value_index' => $optionID, //value of 'Green' index of the attribute 'color'
                                'is_percent' => 1, //fixed/percent price for this option
                                'pricing_value' => $price_percent, //value for the pricing
                            ];
                        }
                    }
                }
            }
            echo '<br/>$configurableProductsData';
            $this->prf($configurableProductsData);
            $configurable_product->setTypeId("configurable"); // Setting Product Type As Configurable
            $configurable_product->setAffectConfigurableProductAttributes(4);
            $this->_objectManager->create('Magento\ConfigurableProduct\Model\Product\Type\Configurable')->setUsedProductAttributeIds($configurabledata, $configurable_product);
            $configurableAttributesData = $configurable_product->getTypeInstance()->getConfigurableAttributesAsArray($configurable_product);
            $this->prf($configurableAttributesData);
            $configurable_product->setNewVariationsAttributeSetId(4); // Setting Attribute Set Id
            $configurable_product->setAssociatedProductIds($simpleProducts);// Setting Associated Products
            $configurable_product->setCanSaveConfigurableAttributes(true);
            $configurable_product->setConfigurableProductsData($configurableProductsData);

            $configurable_product->save();

            //echo "assigned---".$configurable_product->getId();

            return $configurable_product->getId();
        } catch (Exception $ex) {
            //echo "<br>".$ex->getMessage();
            return false;
        }
    }

    public function calculatePercentage($min_price, $price)
    {
        $calculated = (($price - $min_price) / $min_price) * 100;
        $price_percent = min([0, $calculated]);

        return $price_percent;
    }

    public function creationOfMatrixGroup($data)
    {
        echo '<br/>Method: ' . __METHOD__ . '<br/>';
        $today_date = date("m/d/Y");
        $added_date = date('m/d/Y', strtotime("+17 day"));
        //$set_product = $this->_objectManager->create('\Magento\Catalog\Model\Product');
        $set_product = $this->product_obj;

        $product_sku = $data['itemName'];
        $isitemcode = $data['itemCode'];
        $weight_flag = $data['isUseNetMassOrWeight'];
        if ($weight_flag) {
            $weight = 1;
        }
        $item_descriptions = $this->helper_inventoryitem->getItemDescription($isitemcode);
        $product_name = $item_descriptions['itemDescription'];
        $product_descrioption = $item_descriptions['extendedDescription'];
        $prices = $this->prices->getAllPrices($product_sku, $isitemcode, 'GBP');
        try {
            $set_product->setWebsiteIds([1, 2]);
            $set_product->setStoreId(0);
            $set_product->setAttributeSetId(4);
            $set_product->setTypeId('configurable');
            $set_product->setCreatedAt(strtotime('now'));
            // time of product creation
            $set_product->setName($product_name);
            // add Name of Product
            $set_product->setSku($product_sku);
            // add Interprise itemcode
            $set_product->setInterpriseItemCode($isitemcode);
            $set_product->setInterpriseItemType($isitemcode);
            $set_product->setInterpriseStatus($data['status']);
            // add sku hear
            if ($weight_flag) {
                $set_product->setWeight($weight);
            } else {
                $set_product->setProductHasWeight(0);
                $set_product->setWeight(0);
            }

            // add weight of product
            //$set_product->setStatus(1);
            $set_product->setStatus(2);
            $category_id = [4];
            // add your catagory id
//            $set_product->setCategoryIds($category_id);
            // Product Category
            $set_product->setTaxClassId(2);
            // type of tax class
            // (0 - none, 1 - default, 2 - taxable, 4 - shipping)
            $set_product->setVisibility(4);
            // catalog and search visibility
            // $set_product->setManufacturer(28);
            // manufacturer id
            //$set_product->setColor(24);
            //print_r($_product);die;
            $set_product->setNewsFromDate($today_date);
            // product set as new from
            $set_product->setNewsToDate($added_date);
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
            // price in form 100.99
            // $set_product->setCost(88.33);
            // price in form 88.33
            #$set_product->setSpecialPrice($prices['special_price']);
            // special price in form 99.85
            # $set_product->setSpecialFromDate($prices['dateFrom']);
            // special price from (MM-DD-YYYY)
            # $set_product->setSpecialToDate($prices['dateTo']);
            // special price to (MM-DD-YYYY)
            #  $set_product->setMsrpEnabled(1);
            // enable MAP
            #   $set_product->setMsrpDisplayActualPriceType(1);
            // display actual price
            // (1 - on gesture, 2 - in cart, 3 - before order confirmation, 4 - use config)
            # $set_product->setMsrp($prices['msrp']);
            // Manufacturer's Suggested Retail Price
            // $set_product->setMetaTitle('test meta title 2');
            // $set_product->setMetaKeyword('test meta keyword 2');
            $set_product->setMetaDescription($product_descrioption);
//            $set_product->setDescription($product_descrioption);
            //$set_product->setShortDescription($product_descrioption);
            $get_stock_result = $this->inventoryStock->getItemStockFromEndpoint($isitemcode);
            if ($get_stock_result['Status']) {
                $stock_availability = $get_stock_result['entity_id'];
                #if ($stock_availability > 0) {
                $is_in_stock = 1;
                # } else {
                #    $is_in_stock = 0;
                #}
                $set_product->setStockData(
                    [
                        'use_config_manage_stock' => 1,
                        // checkbox for 'Use config settings'
                        'manage_stock' => 1, // manage stock
                        'min_sale_qty' => 1, // Shopping Cart Minimum Qty Allowed
                        'max_sale_qty' => 99, // Shopping Cart Maximum Qty Allowed
                        'is_in_stock' => $is_in_stock, // Stock Availability of product
                        'qty' => $stock_availability, // qty of product
                    ]
                );
            }

            $set_product->save();

//            $this->updateWhosalePrice($set_product);
            $get_product_id = $set_product->getId();

            //  $event = $this->_objectManager->create('Magento\Framework\Event\Manager');
//            $varienObject = new \Magento\Framework\DataObject();
//            $varienObject->setProduct($set_product);
            //    $event->dispatch('catalog_product_save_after', array('product'=>$set_product));
            /* $result_category = $this->helper_category->updateCategoryFromInerprieToMagento($set_product->getId());
              if (!$result_category['Status']) {
              $err_msg = $ex->getMessage();
              $status['Status'] = false;
              $status['error'] = "Product created/updated successfully but error occured: " . $result_category['error'];
              $status['entity_id'] = $get_product_id;
              return $status;
              } */
            // get id of product
            $status['Status'] = true;
            $status['error'] = "";
            $status['entity_id'] = $get_product_id;

            return $status;
        } catch (Exception $ex) {
            $err_msg = $ex->getMessage();
            //echo "<br>fsdfsdfsdfsdfs".$err_msg;
            $status['Status'] = false;
            $status['error'] = "Err msg - " . $err_msg . " in function " . __METHOD__;
            $status['entity_id'] = '';

            return $status;
        }
    }

    public function attributeValueExists($attribute_id, $arg_value)
    {
        $attribute_model = Mage::getModel('eav/entity_attribute');
        $attribute_options_model = Mage::getModel('eav/entity_attribute_source_table');
        $attribute = $attribute_model->load($attribute_id);

        $attribute_table = $attribute_options_model->setAttribute($attribute);
        $options = $attribute_options_model->getAllOptions(false);

        $res = $this->getOptionlist('fragrance');
        if (count($res) > 0) {
            foreach ($res as $k => $vs) {
                if ($vs['label'] == 'Baby Powder') {
                    $value_id = $vs['value'];

                    return $value_id;
                    break;
                }
            }
        }

        return false;
    }

    public function createAttribute($code, $label)
    {
        /** @var EavSetup $eavSetup */
        //echo __METHOD__;
        $setup = $this->_objectManager->create('Magento\Framework\Setup\ModuleDataSetupInterface');
        $setup->startSetup();
        $eavSetup1 = $this->_objectManager->create('Magento\Eav\Setup\EavSetupFactory');
        $eavSetup = $eavSetup1->create(['setup' => $setup]);
        $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, "$code",
            [
                'attribute_set' => 'Default',
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => $label,
                'input' => 'select',
                'class' => '',
                'source' => '',
                // 'global' => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'is_configurable' => true,
                //'default' => 0,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => 'simple,virtual',
            ]
        );
        $this->assignAttributeToAttributeset($code);
    }

    public function assignAttributeToAttributeset($ATTRIBUTE_CODE)
    {
        try {
            $attributeSetId = 4;
            $ATTRIBUTE_GROUP = 'Product Details';
            $eavSetup = $this->_objectManager->create(\Magento\Eav\Setup\EavSetup::class);
            $config = $this->_objectManager->get(\Magento\Catalog\Model\Config::class);
            $attributeManagement = $this->_objectManager->get(\Magento\Eav\Api\AttributeManagementInterface::class);
            $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
            $group_id = $config->getAttributeGroupId($attributeSetId, $ATTRIBUTE_GROUP);
            $attributeManagement->assign(
                'catalog_product',
                $attributeSetId,
                $group_id,
                $ATTRIBUTE_CODE,
                999
            );
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

    public function getcustomimages($fileurl)
    {
        if (!is_dir(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR);
        }
        $fileurl = str_replace(" ", "%20", $fileurl);
        $image_name = uniqid() . '.jpg';
        $file = UPLOAD_DIR . $image_name;
        try {
            $contents = file_get_contents($fileurl);
        } catch (Exception $ex) {
            echo "File download error" . $ex->getMessage();

            return false;
        }
        if (strpos(file_get_contents($fileurl), 'Page Not Found') !== false) {
            return false;
        } else {
            $success = file_put_contents($file, file_get_contents($fileurl));
            chmod($file, 0775);
            $success ? $file : false;

            return $file;
        }
    }
}
