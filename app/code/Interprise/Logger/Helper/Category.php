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

class Category extends Data
{
    public $connection;
    public $resource;
    public $category = [];

    protected $_categoryRepository;
    protected $categoryLinkManagement;

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
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Catalog\Api\CategoryLinkManagementInterface $categoryLinkManagementInterface
    ) {
        $this->_categoryRepository = $categoryRepository;
        $this->categoryLinkManagement = $categoryLinkManagementInterface;
        //$tableName = $resource->getTableName('employee');
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
    public function checkCategoryExist($category_code)
    {
        $category = $this->categoryfactory->create();
        $cate = $category->getCollection()
            ->addAttributeToFilter('interprise_category_code', $category_code)
            ->getFirstItem();
        if (!$cate->getId()) {
             return false;
        } else {
            return $cate->getId();
        }
    }

    public function getCategoryData($itemcode)
    {
        $getCategory = $this->getCurlData('inventory/' . $itemcode . '/category');
        $this->prf($getCategory);
        if (!$getCategory['api_error']) {
                $status['Status']=false;
                $status['error']=$getCategory['results'];
                $status['entity_id']='';
                return $status;
        } else {
            $results = $getCategory['results']['data'];
            $status['Status']=true;
            $status['error']='';
            $status['entity_id']=$results;
            return $status;
        }
    }
    public function categoryProcess($category_code, $array = [])
    {
        $getCategorydata = $this->getCurlData('system/category?categoryCode='.$category_code);
        if ($getCategorydata['api_error']) {
            $category_code = $getCategorydata['results']['data']['attributes']['categoryCode'];
            echo "<br>category code--$category_code";
            $parent_category = $getCategorydata['results']['data']['attributes']['parentCategory'];
            $exist_category = $this->checkCategoryExist($category_code);
            //echo "<br>exjsjsjsj result $exist_category"; die;
            //$array = $this->category;
             $this->category[] = $category_code;
             $stop_flag = false;
            if ($category_code == $parent_category) {
                   $stop_flag = true;
            }
            if ($exist_category || $stop_flag) {
                if ($stop_flag) {
                    $parent_id_default  =$this->getConfig('setup/general/defaultcategory');
                     return ['parent_id'=>$parent_id_default,'categories'=>$this->category];
                } else {
                    return ['parent_id'=>$exist_category,'categories'=>$this->category];
                }

            } else {
                return $this->categoryProcess($parent_category, $array);
            }

        }
    }
    public function cleanCategoryCode($category_code)
    {
        $category_code1 = $category_code;
        $category_code1 = str_replace('&', '%26', $category_code1);
        $category_code1 = str_replace('!', '%21', $category_code1);
        $category_code1 = str_replace('#', '%23', $category_code1);
        $category_code1 = str_replace('$', '%24', $category_code1);
        $category_code1 = str_replace("'", '%27', $category_code1);
        $category_code1 = str_replace("(", '%28', $category_code1);
        $category_code1 = str_replace(")", '%29', $category_code1);
        $category_code1 = str_replace("*", '%2A', $category_code1);
        $category_code1 = str_replace("+", '%2B', $category_code1);
        $category_code1 = str_replace(",", '%2C', $category_code1);
        $category_code1 = str_replace("/", '%2F', $category_code1);
        $category_code1 = str_replace(":", '%3A', $category_code1);
        $category_code1 = str_replace(";", '%3B', $category_code1);
        $category_code1 = str_replace("=", '%3D', $category_code1);
        $category_code1 = str_replace("?", '%3F', $category_code1);
        $category_code1 = str_replace("@", '%40', $category_code1);
        $category_code1 = str_replace("[", '%5B', $category_code1);
        $category_code1 = str_replace("]", '%5D', $category_code1);
        return $category_code1;
    }
    public function makeCategoryTree($tree_array)
    {
        $tree_arra = array_reverse($tree_array['categories']);
        $parent_category = $tree_arra[0];
        unset($tree_arra[0]);
        $parent_id = $tree_array['parent_id'];
        if (count($tree_arra)>0) {
            foreach ($tree_arra as $key => $value) {
                 $category_detail = [];
                 $category_detail['parent_id']=$parent_id;
                 $category_detail['category_name']= $value;
                 $category_detail['url'] = $value;
                 $parent_id = $this->createCategory($category_detail);
            }
        }
    }

    public function createurl($category_code)
    {
            $category_code1 = $category_code;
            $category_code1 = str_replace('&', '_', $category_code1);
            $category_code1 = str_replace('!', '_', $category_code1);
            $category_code1 = str_replace('#', '_', $category_code1);
            $category_code1 = str_replace('$', '_', $category_code1);
            $category_code1 = str_replace("'", '_', $category_code1);
            $category_code1 = str_replace("(", '_', $category_code1);
            $category_code1 = str_replace(")", '_', $category_code1);
            $category_code1 = str_replace("*", '_', $category_code1);
            $category_code1 = str_replace("+", '_', $category_code1);
            $category_code1 = str_replace(",", '_', $category_code1);
            $category_code1 = str_replace("/", '_', $category_code1);
            $category_code1 = str_replace(":", '_', $category_code1);
            $category_code1 = str_replace(";", '_', $category_code1);
            $category_code1 = str_replace("=", '_', $category_code1);
            $category_code1 = str_replace("?", '_', $category_code1);
            $category_code1 = str_replace("@", '_', $category_code1);
            $category_code1 = str_replace("[", '_', $category_code1);
            $category_code1 = str_replace("]", '_', $category_code1);
            $category_code1 = str_replace(" ", '_', $category_code1);
            $category_code1 = strtolower($category_code1);

            return $category_code1;
    }

    public function createCategory($category_detail)
    {
        ini_set("display_errors","1");
        $parentId =  $category_detail['parent_id'];
        $category_name =  $category_detail['category_name'];
        $category_url =  $category_detail['url'];
        $category = $this->categoryfactory->create();
        $category->setName($category_name);
        $category->setParentId($parentId); // 1: root category.
        $category->setIsActive(true);
        $category->setStatus(1);
        $url=strtolower($category_url);
//        $cleanurl = trim(preg_replace(
//            '/ +/',
//            '',
//            preg_replace(
//                '/[^A-Za-z0-9 ]/',
//                '',
//                urldecode((strip_tags($url)))
//            )
//        ));
//        $cleanurl = $cleanurl.'c'.$parentId;
        $cleanurl = $this->createurl(strip_tags($url));
        $category->setUrlKey($cleanurl);
//        $category->setCustomAttributes([
//            'description' => $category_name,
//            'interprise_category_code' => $category_name
//                 ]);

        //$this->_categoryRepository->save($category);
        $category->save();
        echo $category->getId();

    }

    public function createCategoryOld($category_detail)
    {
        $parentId =  $category_detail['parent_id'];
        $category_name =  $category_detail['category_name'];
        $category_url =  $category_detail['url'];
        $storeId = 0;
        $rootCat = $this->categoryfactory->create();
        try {
            $name=ucfirst($category_name);
            $url=strtolower($category_url);
            $cleanurl = trim(preg_replace(
                '/ +/',
                '',
                preg_replace(
                    '/[^A-Za-z0-9 ]/',
                    '',
                    urldecode((strip_tags($url)))
                )
            ));
            $categoryFactory=$this->categoryfactory->create();
            /// Add a new sub category under root category
            $categoryTmp = $categoryFactory->create();
            $categoryTmp->setName($name);
            $categoryTmp->setIsActive(true);
            $categoryTmp->setUrlKey($cleanurl);
            //$categoryTmp->setData('description', 'description');
            $categoryTmp->setParentId($parentId);
            //$categoryTmp->setInterpriseCategoryCode($category_name);
            //$mediaAttribute = array ('image', 'small_image', 'thumbnail');
            //$categoryTmp->setImage('/m2.png', $mediaAttribute, true, false);// Path pub/meida/catalog/category/m2.png
            $categoryTmp->setStoreId($storeId);
            $categoryTmp->setPath($rootCat->getPath());
            $categoryTmp->save();
            $category_id = $categoryTmp->getId();
            return $category_id;
        } catch (Exception $e) {
            return 0;
        }
    }
    public function getOrderProducts($category_id)
    {
        return 999;
    }
    public function saveCategoary($category_id, $product_id, $position = 999)
    {
        $product = $this->productfactory->create()->load($product_id);
        $categoryIds = [];
        $categoryIds[] = $category_id;
        $unique_category = array_unique(
            array_merge(
                $product->getCategoryIds(),
                $categoryIds
            )
        );
        $this->categoryLinkManagement->assignProductToCategories(
            $product->getSku(),
            $unique_category
        );
        $product->save();
    }
    public function updateCategoryFromInerprieToMagento($product_id)
    {
        $_product = $this->productfactory->create()->load($product_id);
        $item_code = $_product->getInterpriseItemCode();
        if (isset($item_code) && $item_code!='') {
            $categories_array = $this->getCategoryData($item_code);
            if (isset($categories_array['Status']) && $categories_array['Status']) {
                $categories = $categories_array['entity_id'];
                if (count($categories)>0) {
                    foreach ($categories as $catkey => $catvalue) {
                        $category_code = $catvalue['attributes']['categoryCode'];
                        $category_description = $catvalue['attributes']['categoryDescription'];
                        $category_id = $this->checkCategoryExist($category_code);
                        if ($category_id!='') {
                            $last_position_of_category = $this->getOrderProducts($category_id);
                            $this->saveCategoary($category_id, $product_id, $last_position_of_category);
                        }
                    }
                }
                $status['Status']=true;
                $status['error']='';
                $status['entity_id']=$product_id;
                return $status;
            } else {
                return $categories_array;
            }
        } else {
            $err_msg = "IS item code not saved in this item";
            $status['Status'] = false;
            $status['error'] = "Err msg - ".$err_msg." in function ".__METHOD__;
            $status['entity_id'] = '';
            return $status;
        }
    }
}
